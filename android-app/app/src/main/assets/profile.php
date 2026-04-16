<?php
// Profile endpoint used by QR "data=".

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$name = isset($_GET['name']) ? trim((string)$_GET['name']) : '';
$email = isset($_GET['email']) ? trim((string)$_GET['email']) : '';

if ($name === '' && $email !== '') {
  $at = strpos($email, '@');
  $name = ($at !== false) ? substr($email, 0, $at) : $email;
}

if ($name === '') $name = 'Member';

$safeName = htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeEmail = htmlspecialchars($email !== '' ? $email : '—', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$initials = '';
$parts = preg_split('/\s+/', trim($name)) ?: [];
$parts = array_values(array_filter($parts, fn($p) => $p !== ''));
if (count($parts) >= 2) $initials = strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
else if (count($parts) === 1) $initials = strtoupper(mb_substr($parts[0], 0, 2));
else $initials = '??';
$safeInitials = htmlspecialchars($initials, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8" />';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />';
echo '<meta name="theme-color" content="#25671E" />';
echo '<title>Profile · Beanthentic</title>';
echo '<style>
  :root{
    --bg1:#f4f7f4; --bg2:#faf8f5; --card:#ffffff;
    --green:#25671E; --green2:#2e8f4a; --text:#111827; --muted:#6b7280;
    --border: rgba(46,111,28,0.14);
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
    color:var(--text);
    background: linear-gradient(180deg, var(--bg1) 0%, var(--bg2) 40%, #fff 100%);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 18px max(16px, env(safe-area-inset-left)) calc(18px + env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-right));
  }
  .wrap{width:100%; max-width:520px;}
  .card{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:20px;
    overflow:hidden;
    box-shadow: 0 4px 24px rgba(22,38,28,0.06), 0 18px 48px rgba(46,111,28,0.10);
  }
  .hero{
    padding: 28px 20px 22px;
    text-align:center;
    color:#fdfaf6;
    background: linear-gradient(145deg, #1a5c34 0%, var(--green2) 45%, #3d7a32 100%);
    position:relative;
  }
  .hero:after{
    content:"";
    position:absolute; left:0; right:0; bottom:0; height:48px;
    background: linear-gradient(180deg, transparent, rgba(255,255,255,0.12));
    pointer-events:none;
  }
  .avatar{
    width:88px; height:88px;
    margin: 0 auto 12px;
    border-radius:999px;
    background: linear-gradient(135deg, rgba(253,250,246,0.95), rgba(227,245,232,0.92));
    border: 3px solid rgba(255,255,255,0.45);
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.16);
    position:relative; z-index:1;
  }
  .avatar span{font-weight:800; letter-spacing:0.06em; color:#1f4d2e; font-size:26px;}
  .kicker{
    margin:0 0 6px;
    font-size:11px; font-weight:700;
    letter-spacing:0.18em; text-transform:uppercase;
    opacity:0.9; position:relative; z-index:1;
  }
  h1{
    margin:0 0 6px;
    font-size:24px;
    line-height:1.2;
    font-weight:800;
    position:relative; z-index:1;
    word-break: break-word;
  }
  .email{
    margin:0;
    font-size:14px;
    opacity:0.92;
    position:relative; z-index:1;
    word-break: break-word;
  }
  .body{padding: 18px 18px 20px;}
  .label{
    margin:0 0 8px;
    font-size:11px; font-weight:800; letter-spacing:0.14em; text-transform:uppercase;
    color:#6b7a66;
  }
  .note{
    margin:0 0 14px;
    color:#4a433c;
    font-size:14px;
    line-height:1.55;
  }
  .pill{
    display:inline-flex; align-items:center; gap:8px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #fdfcfa;
    border: 1px solid rgba(198,157,125,0.28);
    width:100%;
  }
  .pill strong{
    display:block;
    font-size:12px;
    color:#8a7667;
    text-transform:uppercase;
    letter-spacing:0.08em;
  }
  .pill span{
    display:block;
    font-size:15px;
    font-weight:750;
    color:#2c241c;
    word-break: break-word;
  }
  .row{display:grid; gap:10px;}
  .brand{
    margin-top:14px;
    text-align:center;
    color: var(--muted);
    font-size:12px;
  }
  .brand b{color:var(--green)}
 </style></head><body>';
echo '<div class="wrap"><div class="card">';
echo '<div class="hero">';
echo '<div class="avatar"><span>' . $safeInitials . '</span></div>';
echo '<p class="kicker">Beanthentic profile</p>';
echo '<h1>' . $safeName . '</h1>';
echo '<p class="email">' . $safeEmail . '</p>';
echo '</div>';
echo '<div class="body">';
echo '<p class="label">Shared via QR</p>';
echo '<p class="note">This profile was opened from a QR code scan.</p>';
echo '<div class="row">';
echo '<div class="pill"><div><strong>Name</strong><span>' . $safeName . '</span></div></div>';
echo '<div class="pill"><div><strong>Email</strong><span>' . $safeEmail . '</span></div></div>';
echo '</div>';
echo '<div class="brand">Powered by <b>Beanthentic</b></div>';
echo '</div>';
echo '</div></div>';
echo '</body></html>';
?>

