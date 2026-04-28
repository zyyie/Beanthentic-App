package com.beanthentic.app

import android.annotation.SuppressLint
import android.content.ActivityNotFoundException
import android.content.Intent
import android.animation.AnimatorSet
import android.animation.ObjectAnimator
import android.animation.ValueAnimator
import android.graphics.Bitmap
import android.graphics.Color
import android.graphics.drawable.GradientDrawable
import android.os.Build
import android.os.Bundle
import android.os.SystemClock
import android.util.TypedValue
import android.view.Gravity
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.FrameLayout
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.view.animation.AccelerateDecelerateInterpolator
import android.os.Message
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.OnBackPressedCallback
import android.net.Uri
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {
    private lateinit var webView: WebView
    private var exitDialog: AlertDialog? = null

    /** Same default as assets/index.php; use PC LAN IP for physical device + Flask on host. */
    private val flaskDefaultBase: String = "http://10.0.2.2:5000"

    private fun isProbablyEmulator(): Boolean {
        val fp = Build.FINGERPRINT.lowercase()
        val model = Build.MODEL.lowercase()
        return fp.contains("generic") ||
            fp.contains("emulator") ||
            model.contains("google_sdk") ||
            model.contains("sdk_gphone") ||
            model.contains("sdk") && model.contains("x86") ||
            model.contains("emulator") ||
            model.contains("android sdk") ||
            model.contains("x86")
    }

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
                duration = 520L
                // Infinite animation can be expensive on some emulators/phones.
                // We only need a short "loading" motion until the page finishes.
                repeatCount = 1
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
                duration = 520L
                repeatCount = 1
                interpolator = AccelerateDecelerateInterpolator()
            }

            val scaleX = ObjectAnimator.ofFloat(
                icon,
                View.SCALE_X,
                1f,
                1.06f,
                1f
            ).apply {
                duration = 520L
                repeatCount = 1
                interpolator = AccelerateDecelerateInterpolator()
            }

            val scaleY = ObjectAnimator.ofFloat(
                icon,
                View.SCALE_Y,
                1f,
                1.06f,
                1f
            ).apply {
                duration = 520L
                repeatCount = 1
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
            javaScriptCanOpenWindowsAutomatically = true
            setSupportMultipleWindows(true)
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
        // Keep it short so the app feels snappy.
        var minVisibleMs = 140L
        var progressReached100 = false
        // Safety fallback if progress events never hit 100 (common with file://).
        val maxFallbackMs = 800L
        var hideRunnable: Runnable? = null
        var flaskBaseLoadedFromJs = false
        var flaskBase = flaskDefaultBase

        fun stripFragment(url: String?): String = (url ?: "").substringBefore('#')

        fun tryOpenExternalView(uri: Uri): Boolean {
            return try {
                val intent = Intent(Intent.ACTION_VIEW, uri)
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                startActivity(intent)
                true
            } catch (_: ActivityNotFoundException) {
                false
            }
        }

        /**
         * WebView cannot load intent:// URLs (ERR_UNKNOWN_URL_SCHEME). Chrome turns some Maps
         * links into intent://; we launch the resolved app or an https VIEW intent.
         */
        fun tryHandleIntentOrMapsUrl(url: String): Boolean {
            if (url.startsWith("intent:", ignoreCase = true)) {
                try {
                    val intent = Intent.parseUri(url, Intent.URI_INTENT_SCHEME)
                    intent.addCategory(Intent.CATEGORY_BROWSABLE)
                    intent.component = null
                    if (intent.resolveActivity(packageManager) != null) {
                        startActivity(intent)
                        return true
                    }
                    val fb = intent.getStringExtra("browser_fallback_url")
                    if (fb != null && tryOpenExternalView(Uri.parse(fb))) return true
                } catch (_: Throwable) { /* fall through */ }
                val httpsInString = Regex("https://www\\.google\\.com/maps[^\\s#'\"]*").find(url)
                if (httpsInString != null && tryOpenExternalView(Uri.parse(httpsInString.value))) {
                    return true
                }
                val vp = Regex("viewpoint=([0-9.+-]+),([0-9.+-]+)").find(url)
                if (vp != null) {
                    val pano =
                        "https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${vp.groupValues[1]},${vp.groupValues[2]}"
                    if (tryOpenExternalView(Uri.parse(pano))) return true
                }
                return false
            }
            if (url.contains("map_action=pano") &&
                (url.contains("google.com/maps") || url.contains("maps.google.com"))
            ) {
                return tryOpenExternalView(Uri.parse(url))
            }
            return false
        }

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
            // Ensure we never get stuck under the loader.
            hideScheduled = true
            val runnable = Runnable {
                loadingOverlay.visibility = View.GONE
                bounceAnimatorSet.cancel()
            }.also { hideRunnable = it }
            loadingOverlay.postDelayed(runnable, maxFallbackMs)
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

            /**
             * Without this, links with target="_blank" do nothing in WebView.
             * Load the URL in the main WebView instead.
             */
            override fun onCreateWindow(
                view: WebView?,
                isDialog: Boolean,
                isUserGesture: Boolean,
                resultMsg: Message?
            ): Boolean {
                val transport = resultMsg?.obj as? WebView.WebViewTransport ?: return false
                val temp = WebView(this@MainActivity)
                temp.webViewClient = object : WebViewClient() {
                    override fun shouldOverrideUrlLoading(
                        v: WebView?,
                        request: WebResourceRequest?
                    ): Boolean {
                        val url = request?.url?.toString() ?: return true
                        webView.loadUrl(url)
                        return true
                    }
                }
                transport.webView = temp
                resultMsg.sendToTarget()
                return true
            }
        }

        try {
            webView.webViewClient = object : WebViewClient() {
                /**
                 * file:// → http:// navigation often does nothing unless we load explicitly.
                 * Register Farm/Map are served by Flask (register_farm_module.py / maps_module.py) at /register-farm and /maps.
                 */
                override fun shouldOverrideUrlLoading(
                    view: WebView?,
                    request: WebResourceRequest?
                ): Boolean {
                    val uri = request?.url
                    if (uri != null) {
                        val full = uri.toString()
                        if (tryHandleIntentOrMapsUrl(full)) return true
                    }
                    // file:// → http(s):// often needs explicit loadUrl; Flask serves Register Farm/Map from Python modules.
                    if (uri != null) {
                        val sch = uri.scheme?.lowercase() ?: ""
                        // Ensure Register Farm/Map/News (http/https main frame) always loads reliably.
                        if ((sch == "http" || sch == "https") && request?.isForMainFrame != false) {
                            val full = uri.toString()
                            // Physical phone case: JS may have already hardcoded 10.0.2.2.
                            val badBase = "http://10.0.2.2:5000"
                            val rewritten = if (full.startsWith(badBase)) {
                                flaskBase + full.removePrefix(badBase)
                            } else {
                                full
                            }
                            view?.loadUrl(rewritten)
                            return true
                        }
                    }
                    // file:///register-farm or file:///maps from old links — send to Flask modules.
                    if (uri != null && uri.scheme == "file") {
                        val path = uri.path?.trimEnd('/') ?: ""
                        if (path == "/register-farm") {
                            view?.loadUrl("${flaskBase}/register-farm")
                            return true
                        }
                        if (path == "/maps") {
                            view?.loadUrl("${flaskBase}/maps")
                            return true
                        }
                        // Jump between packaged pages (index.php ↔ privacy.php, etc.). Some WebViews
                        // ignore relative links from file:///android_asset/ unless loadUrl is used.
                        val fullPath = uri.path ?: ""
                        if (fullPath.contains("/android_asset/") && request?.isForMainFrame != false) {
                            val dest = uri.toString()
                            val cur = view?.url
                            if (stripFragment(dest) != stripFragment(cur ?: "")) {
                                showLoading()
                                view?.loadUrl(dest)
                                return true
                            }
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

                    // For physical devices, 10.0.2.2 only works on emulator.
                    // Read (or ask for) the correct Flask base URL once.
                    if (!flaskBaseLoadedFromJs && !isProbablyEmulator()) {
                        flaskBaseLoadedFromJs = true
                        webView.evaluateJavascript("localStorage.getItem('beanthentic_flask_base');") { value ->
                            val current = (value as? String)?.trim().orEmpty()
                            if (current.isNotEmpty() && current.startsWith("http")) {
                                flaskBase = current.trimEnd('/')
                                return@evaluateJavascript
                            }

                            val escapedDefault = flaskDefaultBase
                                .replace("\\", "\\\\")
                                .replace("'", "\\'")
                                .replace("\n", " ")
                                .replace("\r", " ")

                            val input = EditText(this@MainActivity).apply {
                                hint = "e.g., http://192.168.1.10:5000"
                                setSingleLine(true)
                                setText(escapedDefault)
                            }

                            AlertDialog.Builder(this@MainActivity)
                                .setTitle("Flask server URL")
                                .setMessage("Enter the URL of your Flask server running on your PC (same Wi‑Fi).")
                                .setView(input)
                                .setCancelable(false)
                                .setPositiveButton("Save") { _, _ ->
                                    val picked = input.text?.toString()?.trim().orEmpty()
                                    if (picked.startsWith("http")) {
                                        flaskBase = picked.trimEnd('/')
                                        val escaped = flaskBase
                                            .replace("\\", "\\\\")
                                            .replace("'", "\\'")
                                            .replace("\n", " ")
                                            .replace("\r", " ")
                                        webView.evaluateJavascript(
                                            "localStorage.setItem('beanthentic_flask_base', '$escaped');",
                                            null
                                        )
                                    }
                                }
                                .setNegativeButton("Use default") { _, _ ->
                                    flaskBase = flaskDefaultBase
                                }
                                .show()
                        }
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


