package com.beanthentic.app

import android.annotation.SuppressLint
import android.animation.AnimatorSet
import android.animation.ObjectAnimator
import android.animation.ValueAnimator
import android.graphics.Bitmap
import android.graphics.Color
import android.graphics.drawable.GradientDrawable
import android.os.Bundle
import android.os.SystemClock
import android.util.TypedValue
import android.view.Gravity
import android.view.View
import android.widget.Button
import android.widget.FrameLayout
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.view.animation.AccelerateDecelerateInterpolator
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {
    private lateinit var webView: WebView
    private var exitDialog: AlertDialog? = null

    /** Same default as assets/index.php; use PC LAN IP for physical device + Flask on host. */
    private val flaskDefaultBase: String = "http://10.0.2.2:5000"

    @SuppressLint("SetJavaScriptEnabled")
    @Suppress("DEPRECATION")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val container = FrameLayout(this)

        // Main WebView
        webView = WebView(this)
        container.addView(
            webView,
            FrameLayout.LayoutParams(
                FrameLayout.LayoutParams.MATCH_PARENT,
                FrameLayout.LayoutParams.MATCH_PARENT
            )
        )

        // Loading overlay (coffee bean in brown)
        val loadingOverlay = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER
            setBackgroundColor(Color.WHITE)
        }

        val icon = ImageView(this@MainActivity).apply {
            setImageResource(R.drawable.coffee_bean_loading)
            scaleType = ImageView.ScaleType.CENTER_INSIDE
            val sizePx = (96 * resources.displayMetrics.density).toInt()
            layoutParams = LinearLayout.LayoutParams(sizePx, sizePx)
        }

        // Coffee bean "jumping" animation
        val bounceAnimatorSet = AnimatorSet().apply {
            val jump = ObjectAnimator.ofFloat(
                icon,
                View.TRANSLATION_Y,
                0f,
                -18f,
                0f
            ).apply {
                duration = 700L
                repeatCount = ValueAnimator.INFINITE
                interpolator = AccelerateDecelerateInterpolator()
            }

            val rotate = ObjectAnimator.ofFloat(
                icon,
                View.ROTATION,
                0f,
                -10f,
                10f,
                0f
            ).apply {
                duration = 700L
                repeatCount = ValueAnimator.INFINITE
                interpolator = AccelerateDecelerateInterpolator()
            }

            val scaleX = ObjectAnimator.ofFloat(
                icon,
                View.SCALE_X,
                1f,
                1.06f,
                1f
            ).apply {
                duration = 700L
                repeatCount = ValueAnimator.INFINITE
                interpolator = AccelerateDecelerateInterpolator()
            }

            val scaleY = ObjectAnimator.ofFloat(
                icon,
                View.SCALE_Y,
                1f,
                1.06f,
                1f
            ).apply {
                duration = 700L
                repeatCount = ValueAnimator.INFINITE
                interpolator = AccelerateDecelerateInterpolator()
            }

            playTogether(jump, rotate, scaleX, scaleY)
        }

        val text = TextView(this@MainActivity).apply {
            text = "Please wait for a moment."
            setTextColor(Color.GRAY)
            setTextSize(TypedValue.COMPLEX_UNIT_SP, 14f)
            paint.isFakeBoldText = false
            paint.isAntiAlias = true
        }

        loadingOverlay.addView(icon)
        loadingOverlay.addView(text)

        container.addView(
            loadingOverlay,
            FrameLayout.LayoutParams(
                FrameLayout.LayoutParams.MATCH_PARENT,
                FrameLayout.LayoutParams.MATCH_PARENT
            )
        )

        loadingOverlay.visibility = View.VISIBLE
        setContentView(container)

        onBackPressedDispatcher.addCallback(
            this,
            object : OnBackPressedCallback(true) {
                override fun handleOnBackPressed() {
                    // If WebView has history, go back instead of exiting.
                    if (::webView.isInitialized && webView.canGoBack()) {
                        webView.goBack()
                        return
                    }

                    // Show exit prompt.
                    if (exitDialog?.isShowing == true) return

                    showExitOptionsDialog()
                }
            }
        )

        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            allowFileAccess = true
            allowContentAccess = true
            mixedContentMode = WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE
            cacheMode = WebSettings.LOAD_DEFAULT
            // Let fetch() load component files from assets (needed for file:// pages)
            allowFileAccessFromFileURLs = true
            allowUniversalAccessFromFileURLs = true
        }

        var loadStartTimeMs = 0L
        var hideScheduled = false
        var minVisibleMs = 300L
        var progressReached100 = false
        val maxFallbackMs = 2000L
        var hideRunnable: Runnable? = null

        fun stripFragment(url: String?): String = (url ?: "").substringBefore('#')

        fun isInPageAnchorNavigation(currentUrl: String?, requestUrl: String?): Boolean {
            val req = requestUrl ?: return false
            if (!req.contains("#")) return false
            return stripFragment(req) == stripFragment(currentUrl)
        }

        fun showLoading() {
            hideScheduled = false
            loadStartTimeMs = SystemClock.uptimeMillis()
            progressReached100 = false
            hideRunnable?.let { loadingOverlay.removeCallbacks(it) }
            loadingOverlay.visibility = View.VISIBLE
            bounceAnimatorSet.start()
        }

        fun scheduleHideAfter(delayMs: Long) {
            if (hideScheduled) return
            hideScheduled = true

            val runnable = hideRunnable ?: Runnable {
                loadingOverlay.visibility = View.GONE
                bounceAnimatorSet.cancel()
            }.also { hideRunnable = it }

            loadingOverlay.postDelayed(runnable, delayMs)
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                // Keep overlay until the WebView reports full progress.
                if (newProgress >= 100) {
                    progressReached100 = true
                    val elapsed = SystemClock.uptimeMillis() - loadStartTimeMs
                    val delay = (minVisibleMs - elapsed).coerceAtLeast(0L)
                    scheduleHideAfter(delay)
                }
            }
        }

        try {
            webView.webViewClient = object : WebViewClient() {
                /**
                 * file:// → http:// navigation often does nothing unless we load explicitly.
                 * GI/Map are served by Flask (gi_module.py / maps_module.py) at /gi and /maps.
                 */
                override fun shouldOverrideUrlLoading(
                    view: WebView?,
                    request: WebResourceRequest?
                ): Boolean {
                    val uri = request?.url
                    // file:// → http(s):// often needs explicit loadUrl; Flask serves GI/Map from Python modules.
                    if (uri != null) {
                        val sch = uri.scheme?.lowercase() ?: ""
                        val fromAssets = view?.url?.startsWith("file:") == true
                        if ((sch == "http" || sch == "https") && fromAssets) {
                            if (request?.isForMainFrame != false) {
                                view?.loadUrl(uri.toString())
                                return true
                            }
                        }
                    }
                    // file:///gi or file:///maps from old /gi links — send to Flask (gi_module / maps_module).
                    if (uri != null && uri.scheme == "file") {
                        val path = uri.path?.trimEnd('/') ?: ""
                        if (path == "/gi") {
                            view?.loadUrl("$flaskDefaultBase/gi")
                            return true
                        }
                        if (path == "/maps") {
                            view?.loadUrl("$flaskDefaultBase/maps")
                            return true
                        }
                    }
                    // Avoid blocking UI for in-page hash navigation (About tabs, etc.).
                    val currentUrl = view?.url
                    val requestUrl = request?.url?.toString()
                    val samePageAnchorNav = isInPageAnchorNavigation(currentUrl, requestUrl)
                    if (!samePageAnchorNav) {
                        showLoading()
                    }
                    return false
                }

                override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                    // Keep hash-only updates smooth and interactive.
                    val samePageAnchorNav = isInPageAnchorNavigation(view?.url, url)
                    if (!samePageAnchorNav) {
                        showLoading()
                    }
                    super.onPageStarted(view, url, favicon)
                }

                override fun onPageFinished(view: WebView?, url: String?) {
                    // Safety fallback: if progress never reaches 100, hide after a max time.
                    if (!progressReached100) {
                        val elapsed = SystemClock.uptimeMillis() - loadStartTimeMs
                        val delay = (maxFallbackMs - elapsed).coerceAtLeast(0L)
                        scheduleHideAfter(delay)
                    }
                    super.onPageFinished(view, url)
                }

                override fun onReceivedError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    error: WebResourceError?
                ) {
                    bounceAnimatorSet.cancel()
                    loadingOverlay.visibility = View.GONE
                    super.onReceivedError(view, request, error)
                }
            }

            showLoading()
            webView.loadUrl("file:///android_asset/index.php")
        } catch (e: Exception) {
            // Fallback: load simple error page so app does not crash
            webView.loadData(
                "<html><body><p>Error loading page.</p></body></html>",
                "text/html",
                "UTF-8"
            )
            loadingOverlay.visibility = View.GONE
            bounceAnimatorSet.cancel()
        }
    }

    private fun showExitOptionsDialog() {
        val card = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            setPadding(dp(20), dp(20), dp(20), dp(16))
            background = GradientDrawable().apply {
                shape = GradientDrawable.RECTANGLE
                cornerRadius = dp(22).toFloat()
                setColor(Color.WHITE)
            }
        }

        val title = TextView(this).apply {
            text = getString(R.string.app_name)
            setTextColor(Color.parseColor("#111827"))
            setTextSize(TypedValue.COMPLEX_UNIT_SP, 20f)
            paint.isFakeBoldText = true
        }

        val message = TextView(this).apply {
            text = "What do you want to do?"
            setTextColor(Color.parseColor("#374151"))
            setTextSize(TypedValue.COMPLEX_UNIT_SP, 15f)
            setPadding(0, dp(10), 0, dp(16))
        }

        val actions = LinearLayout(this).apply {
            orientation = LinearLayout.HORIZONTAL
            gravity = Gravity.CENTER
        }

        val buttonLayoutParams = LinearLayout.LayoutParams(0, dp(46), 1f).apply {
            marginStart = dp(6)
            marginEnd = dp(6)
        }

        fun createActionButton(label: String, colorHex: String, onClick: () -> Unit): Button {
            return Button(this).apply {
                text = label
                setAllCaps(false)
                setTextColor(Color.WHITE)
                setTextSize(TypedValue.COMPLEX_UNIT_SP, 15f)
                background = GradientDrawable().apply {
                    shape = GradientDrawable.RECTANGLE
                    cornerRadius = dp(23).toFloat()
                    setColor(Color.parseColor(colorHex))
                }
                layoutParams = buttonLayoutParams
                setOnClickListener { onClick() }
            }
        }

        val cancelBtn = createActionButton("Cancel", "#6B7280") {
            exitDialog?.dismiss()
        }
        val exitBtn = createActionButton("Exit", "#059669") {
            exitDialog?.dismiss()
            finishAffinity()
        }

        actions.addView(cancelBtn)
        actions.addView(exitBtn)

        card.addView(title)
        card.addView(message)
        card.addView(actions)

        exitDialog = AlertDialog.Builder(this)
            .setView(card)
            .setCancelable(true)
            .create()

        exitDialog?.window?.setBackgroundDrawableResource(android.R.color.transparent)
        exitDialog?.show()
    }

    private fun dp(value: Int): Int = (value * resources.displayMetrics.density).toInt()
}
