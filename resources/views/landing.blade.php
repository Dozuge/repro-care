<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#6C5CE7">
<meta name="description" content="ReproCare — Maternal and Reproductive Health Tracking and Learning System for Community Well-Being. Your baby's growth, your health records, free classes, and your care circle in one warm system.">
<title>ReproCare — Maternal Health Tracking &amp; Learning System</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/reprocare-logo.svg') }}">
<link rel="shortcut icon" href="{{ asset('images/brand/reprocare-logo.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Nunito+Sans:ital,opsz,wght@0,6..12,400..900;1,6..12,400..900&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<style>
/* ============ Foundations & ReproCare Theme ============ */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --cream:#FAF8FD;
  --blush:#F3EEFA;
  --blush-2:#E8DFF5;
  --card:#FFFFFF;
  --plum:#1E1B4B;
  --plum-deep:#140E36;
  --cocoa:rgba(30,27,75,.68);
  --faint:rgba(30,27,75,.42);
  --line:rgba(108,92,231,.14);
  --line-strong:rgba(108,92,231,.28);
  --primary:#6C5CE7;
  --primary-deep:#5E35B1;
  --primary-soft:rgba(108,92,231,.10);
  --primary-glow:rgba(108,92,231,.28);
  --rose:#6C5CE7;
  --rose-deep:#5E35B1;
  --rose-soft:rgba(108,92,231,.10);
  --peach:#EC4899;
  --peach-soft:rgba(236,72,153,.12);
  --honey:#F59E0B;
  --honey-ink:#92400E;
  --sage:#10B981;
  --sage-ink:#065F46;
  --sage-soft:rgba(16,185,129,.14);
}
html{scroll-behavior:smooth;scroll-padding-top:104px}
body{font-family:'Plus Jakarta Sans','Nunito Sans',sans-serif;background:var(--cream);color:var(--plum);font-size:16px;line-height:1.65;-webkit-font-smoothing:antialiased;overflow-x:hidden}
body::after{content:"";position:fixed;inset:0;z-index:2000;pointer-events:none;opacity:.03;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='0.55'/%3E%3C/svg%3E")}
::selection{background:rgba(108,92,231,.22);color:var(--plum)}
h1,h2,h3,.serif{font-family:'Fraunces',serif;font-optical-sizing:auto;letter-spacing:-.015em;font-weight:500}
h2 em,h1 em{font-style:italic;color:var(--primary);font-weight:450}
button{font:inherit;color:inherit;background:none;border:0;cursor:pointer}
a{color:inherit;text-decoration:none}
ul{list-style:none}
svg{display:block}
svg.lucide{width:18px;height:18px;flex:none}
:focus-visible{outline:2px solid var(--primary);outline-offset:3px;border-radius:4px}
.wrap{max-width:1180px;margin-inline:auto;padding-inline:clamp(20px,4vw,44px)}

/* ============ Buttons ============ */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;border-radius:999px;font-weight:700;font-size:.95rem;border:1px solid transparent;transition:transform .25s ease,box-shadow .25s ease,background .25s ease,border-color .25s ease;cursor:pointer}
.btn-primary{background:var(--primary);color:#FFFFFF;padding:14px 28px;box-shadow:0 10px 24px -10px rgba(108,92,231,.5)}
.btn-primary:hover{background:var(--primary-deep);transform:translateY(-2px);box-shadow:0 16px 30px -12px rgba(94,53,177,.6);color:#FFFFFF}
.btn-primary svg{width:16px;height:16px;transition:transform .25s}
.btn-primary:hover svg{transform:translateX(3px)}
.btn-ghost{border-color:var(--line-strong);padding:13px 26px;background:var(--card);color:var(--plum)}
.btn-ghost:hover{border-color:var(--primary);color:var(--primary);transform:translateY(-2px);box-shadow:0 8px 20px -8px rgba(108,92,231,.25)}
.btn-accent{background:var(--peach);color:#FFFFFF;padding:13px 26px;box-shadow:0 10px 24px -10px rgba(236,72,153,.5)}
.btn-accent:hover{background:#DB2777;transform:translateY(-2px);box-shadow:0 16px 30px -12px rgba(219,39,119,.6);color:#FFFFFF}
.btn.sm{padding:9px 18px;font-size:.86rem}

/* ============ Announcement + Nav ============ */
.announce{background:var(--plum-deep);color:#EDE9FE;text-align:center;font-size:.8rem;font-weight:600;letter-spacing:.02em;padding:9px 16px;border-bottom:1px solid rgba(255,255,255,.08)}
.announce svg{width:13px;height:13px;display:inline-block;vertical-align:-2px;color:var(--peach);margin-right:7px}
.nav{position:sticky;top:0;z-index:100;background:rgba(250,248,253,.94);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.nav-inner{display:flex;align-items:center;gap:26px;height:74px}
.brand{display:flex;align-items:center;gap:11px;font-family:'Fraunces',serif;font-weight:600;font-size:1.35rem;letter-spacing:-.02em;color:var(--plum)}
.brand img{width:32px;height:32px;border-radius:8px}
.nav-links{display:flex;align-items:center;gap:26px;margin-left:auto}
.nav-links a{font-size:.92rem;font-weight:700;color:var(--cocoa);position:relative;transition:color .25s}
.nav-links a:hover{color:var(--primary)}
.nav-links a::after{content:"";position:absolute;left:0;bottom:-6px;height:2px;width:0;background:var(--primary);transition:width .3s ease;border-radius:2px}
.nav-links a:hover::after{width:100%}
.nav-book{display:none}
.nav-actions{display:flex;align-items:center;gap:12px}
.menu-btn{display:none;width:42px;height:42px;border:1px solid var(--line-strong);border-radius:50%;place-items:center;color:var(--plum)}
.menu-btn .ic-x{display:none}
body.nav-open .menu-btn .ic-x{display:block}
body.nav-open .menu-btn .ic-menu{display:none}

@media(max-width:980px){
  .nav-inner{height:66px}
  .menu-btn{display:grid}
  .nav-links{position:fixed;top:66px;left:0;right:0;background:var(--cream);border-bottom:1px solid var(--line);flex-direction:column;align-items:stretch;gap:0;padding:8px clamp(20px,4vw,44px) 22px;transform:translateY(-12px);opacity:0;visibility:hidden;pointer-events:none;transition:.3s ease;box-shadow:0 20px 40px -15px rgba(30,27,75,.15)}
  body.nav-open .nav-links{transform:none;opacity:1;visibility:visible;pointer-events:auto}
  .nav-links a{padding:14px 0;border-bottom:1px solid var(--line);font-size:1rem}
  .nav-links a::after{display:none}
  .nav-book{display:block;color:var(--primary-deep);font-weight:800}
}
@media(max-width:640px){.nav-actions .btn-book-nav{display:none}}

/* ============ Hero ============ */
.hero{padding-block:clamp(48px,7vw,88px) clamp(60px,8vw,100px);position:relative;overflow:hidden}
.hero::before{content:"";position:absolute;top:-120px;right:-120px;width:450px;height:450px;background:radial-gradient(circle,rgba(108,92,231,.12) 0%,transparent 70%);border-radius:50%;pointer-events:none}
.hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:clamp(40px,5vw,76px);align-items:center}
.eyebrow{display:flex;align-items:center;gap:10px;font-size:.74rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--primary);margin-bottom:22px}
.live-dot{width:8px;height:8px;border-radius:50%;background:var(--peach);display:inline-block;animation:blink 2.2s ease-in-out infinite}
@keyframes blink{50%{opacity:.3}}
h1{font-size:clamp(2.4rem,5vw,3.95rem);line-height:1.08;font-weight:480;color:var(--plum)}
.hero-sub{margin-top:22px;font-size:1.04rem;color:var(--cocoa);max-width:52ch;line-height:1.7}
.hero-cta{display:flex;flex-wrap:wrap;gap:14px;margin-top:32px}
.hero-stats{display:flex;flex-wrap:wrap;gap:clamp(20px,3vw,42px);margin-top:clamp(28px,4vw,44px);border-top:1px solid var(--line);padding-top:22px}
.hero-stats div{display:flex;flex-direction:column;gap:3px}
.hero-stats div+div{border-left:1px solid var(--line);padding-left:clamp(20px,3vw,42px)}
.hero-stats b{font-family:'Fraunces',serif;font-size:1.8rem;font-weight:600;line-height:1;color:var(--primary)}
.hero-stats span{font-size:.78rem;color:var(--cocoa);max-width:16ch;font-weight:600}

.hero-visual{position:relative;width:min(450px,100%);justify-self:end}
.arch-outline{position:absolute;inset:0;transform:translate(16px,-16px);border:1.5px solid rgba(108,92,231,.35);border-radius:999px 999px 30px 30px;z-index:0}
.hero-photo{position:relative;z-index:1;border-radius:999px 999px 28px 28px;overflow:hidden;aspect-ratio:4/5;box-shadow:0 35px 75px -35px rgba(108,92,231,.45)}

/* ============ Illustrated art slots ============ */
[data-art]{position:relative;overflow:hidden}
.art-fill[data-art]{position:absolute;inset:0}
[data-art]>svg{position:absolute;inset:0;width:100%;height:100%;display:block}
.zoomable>svg{transition:transform .7s cubic-bezier(.22,.8,.24,1)}
.zoomable:hover>svg{transform:scale(1.05)}
.floaty{animation:floaty 5s ease-in-out infinite;transform-box:fill-box;transform-origin:center}
@keyframes floaty{50%{transform:translateY(-7px)}}

.float-chip{position:absolute;top:28px;right:-12px;z-index:3;background:var(--card);border-radius:999px;padding:10px 18px;display:flex;align-items:center;gap:11px;box-shadow:0 18px 40px -18px rgba(30,27,75,.35);border:1px solid var(--line)}
.float-chip svg{width:20px;height:20px;color:var(--peach);animation:beat var(--pd,1.15s) ease-in-out infinite}
@keyframes beat{0%,100%{transform:scale(1)}14%{transform:scale(1.26)}28%{transform:scale(1)}}
.float-chip b{font-family:'Fraunces',serif;font-size:1.1rem;display:block;line-height:1.1;color:var(--plum)}
.float-chip span{font-size:.68rem;color:var(--cocoa);font-weight:700}

.appt-card{position:absolute;left:-72px;bottom:30px;z-index:3;width:min(342px,88%);background:var(--card);border-radius:22px;padding:20px;box-shadow:0 30px 60px -30px rgba(108,92,231,.4);border:1px solid var(--line)}
.appt-card.flash{animation:flashRing 1.7s ease}
@keyframes flashRing{0%,100%{box-shadow:0 30px 60px -30px rgba(108,92,231,.4)}25%{box-shadow:0 0 0 8px rgba(108,92,231,.3),0 30px 60px -30px rgba(108,92,231,.4)}60%{box-shadow:0 0 0 3px rgba(108,92,231,.16),0 30px 60px -30px rgba(108,92,231,.4)}}
.ap-head{display:flex;gap:12px;align-items:center;margin-bottom:6px}
.ap-ic{width:42px;height:42px;border-radius:14px;background:var(--primary-soft);color:var(--primary);display:grid;place-items:center;flex:none}
.ap-ic svg{width:20px;height:20px}
.ap-head b{font-family:'Fraunces',serif;font-size:1.1rem;display:block;line-height:1.2;color:var(--plum)}
.ap-head span{font-size:.74rem;color:var(--cocoa);font-weight:600}
.appt-card label{display:block;font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--cocoa);margin:13px 0 6px}
.appt-card input[type="date"]{width:100%;border:1px solid var(--line-strong);border-radius:12px;padding:11px 13px;font:inherit;font-size:.9rem;background:var(--cream);color:var(--plum);outline:none;transition:border-color .25s}
.appt-card input[type="date"]:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-soft)}
.ap-times{display:grid;grid-template-columns:repeat(4,1fr);gap:7px}
.ap-time{border:1px solid var(--line-strong);border-radius:10px;padding:9px 2px;font-size:.76rem;font-weight:800;color:var(--cocoa);text-align:center;transition:.2s}
.ap-time:hover{border-color:var(--primary);color:var(--primary)}
.ap-time.on{background:var(--primary);color:#FFFFFF;border-color:var(--primary)}
.ap-err{display:flex;align-items:center;gap:7px;color:#DC2626;font-size:.8rem;font-weight:700;margin-top:10px}
.ap-err svg{width:14px;height:14px}
#apBook{width:100%;margin-top:15px}
.ap-fine{display:flex;gap:7px;align-items:center;font-size:.73rem;color:var(--faint);margin-top:11px;font-weight:600}
.ap-fine svg{width:13px;height:13px}
.ap-done{text-align:center;padding:8px 0 2px}
.ok-big{width:56px;height:56px;border-radius:50%;background:var(--sage-soft);color:var(--sage-ink);display:grid;place-items:center;margin:0 auto 14px}
.ok-big svg{width:26px;height:26px}
.ap-done b{font-family:'Fraunces',serif;font-size:1.35rem;display:block;color:var(--plum)}
.ap-when{font-size:.95rem;font-weight:800;color:var(--primary);margin-top:5px}
.ap-where{font-size:.8rem;color:var(--cocoa);margin-top:3px}
.ap-done-btns{display:flex;gap:10px;justify-content:center;margin-top:18px}
.link-btn{text-decoration:underline;text-underline-offset:3px;font-weight:800;font-size:.86rem;color:var(--cocoa)}
.link-btn:hover{color:var(--primary)}
@media(max-width:1020px){
  .hero-grid{grid-template-columns:1fr}
  .hero-visual{justify-self:center;margin-top:8px}
  .appt-card{position:relative;left:auto;bottom:auto;margin:-44px auto 0;width:min(430px,100%)}
  .float-chip{right:14px}
}

/* ============ Marquee ============ */
.marquee{background:var(--blush);border-block:1px solid var(--line);overflow:hidden}
.mq-track{display:flex;width:max-content;animation:mq 32s linear infinite}
.marquee:hover .mq-track{animation-play-state:paused}
@keyframes mq{to{transform:translateX(-50%)}}
.mq-item{display:inline-flex;align-items:center;gap:12px;padding:14px 28px;font-size:.76rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--cocoa);white-space:nowrap}
.mq-item svg{width:13px;height:13px;color:var(--primary)}

/* ============ Section scaffolding ============ */
.sec{padding-block:clamp(72px,9vw,116px)}
.tint{background:var(--blush);border-block:1px solid var(--line)}
.deep{background:var(--plum-deep);color:#F3EEFA}
.kicker{display:flex;align-items:center;gap:14px;margin-bottom:20px}
.k-rule{width:44px;height:1.5px;background:var(--primary)}
.k-label{font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;font-weight:800;color:var(--primary)}
.sec-head{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,.85fr);gap:clamp(28px,4vw,64px);align-items:end;margin-bottom:clamp(38px,5vw,58px)}
h2{font-size:clamp(1.9rem,3.4vw,2.85rem);line-height:1.12;font-weight:480;color:var(--plum)}
.sec-lede{color:var(--cocoa);font-size:.98rem;max-width:46ch;padding-bottom:6px;line-height:1.65}
.deep .k-label{color:rgba(237,233,254,.7)}
.deep .k-rule{background:var(--peach)}
.deep h2{color:#FFFFFF}
.deep h2 em{color:var(--peach)}
.deep .sec-lede{color:rgba(237,233,254,.75)}

/* ============ Categories ============ */
.cat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:20px}
.cat{background:var(--card);border:1px solid var(--line);border-radius:26px;padding:12px 12px 18px;display:flex;flex-direction:column;transition:transform .35s ease,box-shadow .35s ease,border-color .3s}
.cat:hover{transform:translateY(-7px);box-shadow:0 26px 50px -28px rgba(108,92,231,.35);border-color:var(--primary)}
.cat-img{border-radius:999px 999px 16px 16px;overflow:hidden;aspect-ratio:4/5}
.cat-t{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:14px}
.cat-t b{font-family:'Fraunces',serif;font-size:1.06rem;font-weight:560;line-height:1.25;color:var(--plum)}
.cat-go{width:30px;height:30px;border-radius:50%;background:var(--primary-soft);color:var(--primary);display:grid;place-items:center;flex:none;opacity:0;transform:translate(-4px,4px);transition:.3s}
.cat-go svg{width:13px;height:13px}
.cat:hover .cat-go{opacity:1;transform:none}
.cat-s{font-size:.78rem;color:var(--cocoa);margin-top:3px;font-weight:600}
@media(max-width:1020px){.cat-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:640px){.cat-grid{grid-template-columns:repeat(2,1fr)}.cat-s{display:none}}

/* ============ Meet your baby ============ */
.calc-row{display:flex;flex-wrap:wrap;align-items:center;gap:14px;margin-bottom:24px}
.calc-lab{font-weight:800;font-size:.92rem;color:var(--plum)}
.calc-in{border:1px solid var(--line-strong);border-radius:999px;padding:11px 18px;font:inherit;font-size:.9rem;background:var(--card);color:var(--plum);outline:none;transition:border-color .25s}
.calc-in:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-soft)}
.calc-chip{display:inline-flex;align-items:center;gap:9px;background:var(--card);border:1px solid var(--line);padding:10px 18px;border-radius:999px;font-size:.88rem;font-weight:700;margin-left:auto;color:var(--plum)}
.calc-chip svg{width:15px;height:15px;color:var(--primary)}
@media(max-width:700px){.calc-chip{margin-left:0}}
.jcard{background:var(--card);border:1px solid var(--line);border-radius:28px;padding:18px;display:grid;grid-template-columns:300px minmax(0,1fr);gap:22px;box-shadow:0 28px 56px -35px rgba(108,92,231,.3)}
.j-stage{position:relative;background:var(--blush);border-radius:999px 999px 26px 26px;overflow:hidden;min-height:400px;display:flex;align-items:flex-end;justify-content:center}
.j-stage svg.baby{width:100%;height:100%;padding-bottom:4px}
.st-chip{position:absolute;top:18px;right:14px;z-index:2;background:var(--card);border-radius:999px;padding:8px 14px;display:flex;gap:8px;align-items:center;box-shadow:0 12px 28px -14px rgba(30,27,75,.35);border:1px solid var(--line)}
.st-chip svg{width:16px;height:16px;color:var(--peach);animation:beat var(--pd,1.15s) ease-in-out infinite}
.st-chip.still svg{animation:none;opacity:.35}
.st-chip b{font-family:'Fraunces',serif;font-size:1rem;line-height:1.1;color:var(--plum)}
.st-chip span{font-size:.64rem;color:var(--cocoa);font-weight:800;letter-spacing:.08em;text-transform:uppercase}
#babyG{transform-box:fill-box;transform-origin:50% 100%;transition:transform .45s cubic-bezier(.22,.8,.24,1)}
#babyInner{transform-box:fill-box;transform-origin:50% 100%;animation:breathe 3.6s ease-in-out infinite}
@keyframes breathe{50%{transform:scale(1.016)}}
.j-info{display:flex;flex-direction:column;padding:16px 12px 6px;min-width:0}
.j-week{display:flex;align-items:baseline;gap:12px;flex-wrap:wrap}
.j-week b{font-family:'Fraunces',serif;font-size:3.2rem;font-weight:560;line-height:1;color:var(--plum)}
.j-week span{font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;font-weight:800;color:var(--faint)}
.j-tri{font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:6px 13px;border-radius:999px;border:1px solid;white-space:nowrap}
.j-size{font-family:'Fraunces',serif;font-style:italic;font-size:1.45rem;color:var(--primary-deep);margin-top:8px;line-height:1.3}
.j-note{color:var(--cocoa);margin-top:12px;max-width:56ch;font-size:.95rem}
.j-tip{display:flex;gap:10px;align-items:flex-start;background:var(--primary-soft);color:var(--primary-deep);padding:12px 15px;border-radius:14px;font-size:.9rem;font-weight:700;margin-top:14px}
.j-tip svg{width:16px;height:16px;margin-top:2px;color:var(--primary)}
.j-stats{display:flex;flex-wrap:wrap;gap:10px 30px;border-top:1px dashed var(--line);margin-top:auto;padding-top:16px}
.j-stat{display:flex;align-items:center;gap:10px}
.j-stat svg{width:18px;height:18px;color:var(--primary)}
.j-stat.still svg{animation:none;opacity:.35}
.j-stat b{font-family:'Fraunces',serif;font-size:1.3rem;font-weight:560;line-height:1;color:var(--plum)}
.j-stat span{font-size:.66rem;letter-spacing:.1em;text-transform:uppercase;color:var(--cocoa);font-weight:800;max-width:11ch;line-height:1.4}
#jBpmBox svg{animation:beat var(--pd,1.15s) ease-in-out infinite;color:var(--peach)}
.scrub{grid-column:1/-1;padding:6px 14px 18px}
.scrub-hint{font-size:.68rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--faint);margin-bottom:6px;display:flex;align-items:center;gap:8px}
.scrub-hint svg{width:13px;height:13px}
.scrub-track{position:relative;height:56px;cursor:pointer;touch-action:none}
.scrub-line{position:absolute;left:0;right:0;top:20px;height:5px;border-radius:99px;background:var(--blush-2)}
.scrub-fill{position:absolute;left:0;top:20px;height:5px;border-radius:99px;background:var(--primary);width:0}
.scrub-tick{position:absolute;top:30px;transform:translateX(-50%);text-align:center}
.scrub-tick i{display:block;margin:0 auto 2px;width:2px;height:10px;background:var(--line-strong)}
.scrub-tick em{font-style:normal;font-size:.7rem;font-weight:800;color:var(--faint)}
.scrub-handle{position:absolute;top:22px;left:0;width:46px;height:46px;margin:-23px 0 0 -23px;border-radius:50%;background:var(--card);border:2.5px solid var(--primary);display:grid;place-items:center;font-family:'Fraunces',serif;font-weight:600;font-size:1.02rem;color:var(--plum);box-shadow:0 10px 22px -6px rgba(108,92,231,.5);cursor:grab;transition:border-color .3s}
.scrub-handle:active{cursor:grabbing}
@media(max-width:900px){.jcard{grid-template-columns:1fr}.j-stage{min-height:330px}}

/* ============ Tracking ============ */
.track-grid{display:grid;grid-template-columns:minmax(0,1.12fr) minmax(0,.88fr);gap:clamp(24px,3vw,44px);align-items:start}
.chart-card,.panel-card{background:var(--card);border:1px solid var(--line);border-radius:24px;padding:clamp(18px,2.4vw,26px);box-shadow:0 24px 50px -32px rgba(108,92,231,.25)}
.chart-head{display:flex;align-items:baseline;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px}
.chart-head h3{font-size:1.18rem;font-weight:560;color:var(--plum)}
.chart-sub{font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--faint);font-weight:800}
.chart-wrap{position:relative}
#wChart{width:100%;height:auto}
#wChart .wcPatient{stroke-dasharray:1;stroke-dashoffset:1}
#wChart.drawn .wcPatient{stroke-dashoffset:0;transition:stroke-dashoffset 1.4s cubic-bezier(.4,0,.2,1) .2s}
#wChart .dot{opacity:0;transition:opacity .4s ease}
#wChart.drawn .dot{opacity:1}
#wChart.drawn .dot:nth-of-type(2){transition-delay:.4s}#wChart.drawn .dot:nth-of-type(3){transition-delay:.5s}
#wChart.drawn .dot:nth-of-type(4){transition-delay:.6s}#wChart.drawn .dot:nth-of-type(5){transition-delay:.7s}
#wChart.drawn .dot:nth-of-type(6){transition-delay:.8s}#wChart.drawn .dot:nth-of-type(7){transition-delay:.9s}
#wChart.drawn .dot:nth-of-type(8){transition-delay:1s}#wChart.drawn .dot:nth-of-type(9){transition-delay:1.1s}
#wChart.drawn .dot:nth-of-type(10){transition-delay:1.2s}
#wChart .flag-note{opacity:0;transition:opacity .5s ease 1.3s}
#wChart.drawn .flag-note{opacity:1}
#wChart #wcBand{opacity:0;transition:opacity 1s ease}
#wChart.drawn #wcBand{opacity:1}
.tt{position:absolute;pointer-events:none;background:var(--plum-deep);color:#EDE9FE;padding:10px 13px;border-radius:12px;transform:translate(-50%,calc(-100% - 12px));opacity:0;transition:opacity .2s;min-width:150px;z-index:5;border:1px solid rgba(255,255,255,.12)}
.tt.on{opacity:1}
.tt b{font-family:'Fraunces',serif;font-size:1rem;display:block;color:#FFFFFF}
.tt .tt-k{font-size:.82rem;color:rgba(237,233,254,.8);display:block;margin-top:2px}
.tt .tt-s{display:inline-block;font-size:.62rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;padding:3px 9px;border-radius:999px;margin-top:7px}
.tt .tt-s.ok{background:rgba(16,185,129,.25);color:#A7F3D0}
.tt .tt-s.above{background:rgba(236,72,153,.25);color:#FBCFE8}
.tt .tt-s.below{background:rgba(129,140,248,.25);color:#C7D2FE}
.legend{display:flex;flex-wrap:wrap;gap:8px 22px;margin-top:16px}
.lg{display:inline-flex;align-items:center;gap:9px;font-size:.76rem;color:var(--cocoa);font-weight:700}
.sw{display:inline-block}
.sw-band{width:15px;height:10px;background:rgba(16,185,129,.25);border-radius:3px}
.sw-line{width:18px;height:0;border-top:2.5px solid var(--primary);border-radius:2px}
.sw-flag{width:9px;height:9px;background:var(--peach);border-radius:50%}
.chart-foot{display:flex;gap:9px;align-items:flex-start;margin-top:16px;font-size:.78rem;color:var(--cocoa);border-top:1px dashed var(--line);padding-top:14px}
.chart-foot svg{width:14px;height:14px;margin-top:2px;color:var(--faint)}
.log-row{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-top:14px;padding-top:14px;border-top:1px dashed var(--line)}
.log-row label{font-size:.8rem;font-weight:800;color:var(--cocoa)}
.log-row input{width:110px;border:1px solid var(--line-strong);border-radius:999px;padding:9px 15px;font:inherit;font-size:.9rem;background:var(--cream);color:var(--plum);outline:none;transition:border-color .25s}
.log-row input:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-soft)}
.log-msg{flex-basis:100%;font-size:.84rem;font-weight:700}
.log-msg.ok{color:var(--sage-ink)}
.log-msg.warn{color:var(--peach)}

.panel-cap{font-size:.7rem;letter-spacing:.16em;text-transform:uppercase;font-weight:800;color:var(--faint);margin-bottom:6px}
.appts li{display:grid;grid-template-columns:104px 1fr auto;gap:14px;align-items:baseline;padding:15px 2px;border-top:1px solid var(--line)}
.appts li:first-child{border-top:0}
.appts li.ap-new{background:var(--primary-soft);border-radius:12px;padding-inline:12px;border-top:0;margin-top:6px}
.ap-d{font-size:.82rem;font-weight:800;color:var(--cocoa);white-space:nowrap}
.ap-d i{font-style:normal;color:var(--primary)}
.ap-n{font-size:.94rem;font-weight:700;color:var(--plum)}
.ap-l{font-size:.76rem;color:var(--cocoa)}
.rem-row{padding:15px 2px;border-top:1px solid var(--line)}
.rem-row>div{display:flex;align-items:center;justify-content:space-between;gap:16px;width:100%}
.rem-row b{display:block;font-size:.94rem;color:var(--plum)}
.rem-row span{font-size:.78rem;color:var(--cocoa)}
.switch{width:46px;height:25px;border-radius:999px;background:#DDD6FE;position:relative;transition:background .3s;flex:none}
.switch::after{content:"";position:absolute;top:3px;left:3px;width:19px;height:19px;border-radius:50%;background:var(--card);transition:transform .3s;box-shadow:0 1px 3px rgba(30,27,75,.25)}
.switch[aria-checked="true"]{background:var(--primary)}
.switch[aria-checked="true"]::after{transform:translateX(21px)}
.team{display:flex;align-items:center;gap:14px;margin-top:16px;padding-top:16px;border-top:1px dashed var(--line)}
.team-av{display:flex;flex:none}
.team-av span{width:34px;height:34px;border-radius:50%;border:2px solid var(--card);display:grid;place-items:center;color:#FFFFFF;font-weight:800;font-size:.72rem}
.team-av span+span{margin-left:-9px}
.team p{font-size:.8rem;color:var(--cocoa);font-weight:600;line-height:1.5}

/* ============ Kick counter ============ */
.kick-card{margin-top:clamp(40px,5vw,60px);background:var(--card);border:1px solid var(--line);border-radius:28px;padding:clamp(26px,3.5vw,44px);display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:clamp(28px,4vw,60px);align-items:center;box-shadow:0 24px 50px -32px rgba(108,92,231,.25)}
.kick-copy h3{font-size:clamp(1.5rem,2.4vw,1.9rem);font-weight:520;color:var(--plum)}
.kick-copy h3 em{font-style:italic;color:var(--primary)}
.kick-copy>p{color:var(--cocoa);margin-top:12px;max-width:52ch}
.k-steps{margin-top:20px;display:flex;flex-direction:column;gap:12px}
.k-step{display:flex;gap:14px;align-items:flex-start}
.k-n{width:30px;height:30px;border-radius:50%;background:var(--primary-soft);color:var(--primary);font-weight:800;font-size:.85rem;display:grid;place-items:center;flex:none}
.k-step p{font-size:.92rem;color:var(--cocoa);font-weight:600}
.k-warn{margin-top:20px;display:flex;gap:11px;background:rgba(245,158,11,.12);color:var(--honey-ink);border-radius:14px;padding:13px 16px;font-size:.85rem;font-weight:600}
.k-warn svg{width:16px;height:16px;margin-top:2px;flex:none;color:var(--honey)}
.kick-wrap{display:flex;flex-direction:column;align-items:center;gap:14px}
.kick-btn{width:196px;height:196px;border-radius:50%;position:relative;background:var(--blush);transition:transform .15s ease,background .4s ease}
.kick-btn:not(.done):active{transform:scale(.95)}
.kick-btn.done{background:var(--sage-soft);pointer-events:none}
.kick-btn .ring{position:absolute;inset:0;width:100%;height:100%;pointer-events:none}
.kick-mid{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;pointer-events:none}
.kick-mid b{font-family:'Fraunces',serif;font-size:3rem;font-weight:560;line-height:1;color:var(--plum)}
.kick-mid small{font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--cocoa)}
.kick-mid .done-in{display:none;align-items:center;gap:8px;color:var(--sage-ink)}
.kick-mid .done-in svg{width:22px;height:22px}
.kick-mid .done-in b{font-size:2rem;color:var(--sage-ink)}
.kick-btn.done .kick-mid b:not(.t),.kick-btn.done .kick-mid small{display:none}
.kick-btn.done .done-in{display:inline-flex}
.kick-time{font-family:'Fraunces',serif;font-size:1.35rem;font-variant-numeric:tabular-nums;color:var(--plum)}
.kick-time span{font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--faint);font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;margin-left:8px}
.kick-stat{font-size:.82rem;color:var(--cocoa);font-weight:700;text-align:center;min-height:2.6em;max-width:26ch}
.kick-actions{display:none;gap:10px;flex-wrap:wrap;justify-content:center}
.kick-actions.on{display:flex}
@media(max-width:860px){.kick-card{grid-template-columns:1fr}.kick-wrap{margin-inline:auto}}

/* ============ Classes ============ */
.plan-chip{display:inline-flex;align-items:center;gap:9px;background:var(--primary-soft);color:var(--primary);border:1px solid rgba(108,92,231,.25);padding:9px 17px;border-radius:999px;font-size:.8rem;font-weight:800}
.plan-chip svg{width:15px;height:15px}
.plan-chip b{font-family:'Fraunces',serif;font-size:1.05rem;color:var(--primary)}
.pgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.pcard{background:var(--card);border:1px solid var(--line);border-radius:26px;padding:12px 12px 18px;display:flex;flex-direction:column;transition:transform .35s ease,box-shadow .35s ease,border-color .3s}
.pcard:hover{transform:translateY(-7px);box-shadow:0 28px 54px -30px rgba(108,92,231,.35);border-color:var(--primary)}
.pcard-img{position:relative;aspect-ratio:5/4;border-radius:999px 999px 16px 16px;overflow:hidden}
.tag{position:absolute;top:14px;left:14px;background:rgba(255,255,255,.94);color:var(--plum);font-size:.62rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;padding:5px 11px;border-radius:999px;z-index:2;box-shadow:0 4px 10px rgba(0,0,0,.06)}
.free{position:absolute;top:14px;right:14px;background:var(--primary);color:#FFFFFF;font-size:.68rem;font-weight:800;letter-spacing:.04em;padding:5px 13px;border-radius:999px;z-index:2}
.pcard-body{padding:16px 8px 0;display:flex;flex-direction:column;flex:1}
.p-meta{display:flex;justify-content:space-between;align-items:center;font-size:.78rem;color:var(--cocoa);font-weight:700}
.rate{color:var(--honey);font-weight:800;display:inline-flex;gap:5px;align-items:center}
.rate svg{width:13px;height:13px;fill:currentColor}
.pcard-body h3{font-size:1.24rem;font-weight:560;margin-top:8px;line-height:1.25;color:var(--plum)}
.pcard-body>p{color:var(--cocoa);font-size:.9rem;margin-top:7px}
.pcard-foot{margin-top:auto;padding-top:18px;display:flex;gap:10px;align-items:stretch}
.start{flex:1}
.start .s-in{display:none;align-items:center;gap:7px}
.start .s-in svg{width:15px;height:15px}
.start.added{background:var(--primary-soft);color:var(--primary);border:1px solid rgba(108,92,231,.35)}
.start.added:hover{transform:none;box-shadow:none;background:var(--primary-soft)}
.start.added .s-go{display:none}
.start.added .s-in{display:inline-flex}
.save{width:46px;height:46px;border-radius:50%;border:1px solid var(--line-strong);display:grid;place-items:center;color:var(--cocoa);transition:.25s;flex:none}
.save:hover{border-color:var(--primary);color:var(--primary)}
.save.saved{background:var(--primary-soft);border-color:rgba(108,92,231,.4);color:var(--primary)}
.save.saved svg{fill:currentColor}
@media(max-width:980px){.pgrid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:620px){.pgrid{grid-template-columns:1fr}}

/* quiz */
.quiz-card{margin:clamp(44px,5vw,64px) auto 0;max-width:760px;background:var(--card);border:1px solid var(--line);border-radius:24px;padding:clamp(20px,2.6vw,30px);box-shadow:0 24px 50px -30px rgba(108,92,231,.25)}
.q-top{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.q-chip{font-size:.66rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--primary);border:1px solid rgba(108,92,231,.4);padding:5px 12px;border-radius:999px;background:var(--primary-soft)}
.q-meta{font-size:.78rem;color:var(--cocoa);font-weight:700}
.q-prog{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.q-dots{display:flex;gap:7px}
.q-dots i{width:9px;height:9px;border-radius:50%;border:1.5px solid var(--line-strong);transition:.3s}
.q-dots i.cur{border-color:var(--primary);background:var(--primary)}
.q-dots i.ok{border-color:var(--sage);background:var(--sage)}
.q-dots i.no{border-color:var(--peach);background:var(--peach)}
.q-count{font-size:.74rem;color:var(--cocoa);letter-spacing:.08em;text-transform:uppercase;font-weight:800}
.q-q{font-size:1.28rem;line-height:1.35;font-weight:540;margin-bottom:20px;color:var(--plum)}
.q-opts{display:flex;flex-direction:column;gap:10px}
.opt{display:flex;align-items:baseline;gap:14px;text-align:left;padding:14px 16px;border:1px solid var(--line);border-radius:13px;background:var(--cream);font-size:.93rem;transition:border-color .25s,background .25s,transform .2s;color:var(--plum)}
.opt:hover:not(:disabled){border-color:var(--primary);transform:translateX(3px)}
.opt:disabled{cursor:default}
.opt-l{font-family:'Fraunces',serif;font-style:italic;color:var(--faint);flex:none}
.opt.correct{border-color:var(--sage);background:var(--sage-soft)}
.opt.correct .opt-l{color:var(--sage-ink)}
.opt.wrong{border-color:var(--peach);background:var(--peach-soft)}
.opt.wrong .opt-l{color:var(--peach)}
.q-fb{display:flex;gap:11px;align-items:flex-start;margin-top:18px;padding:14px 16px;border-radius:13px;font-size:.88rem}
.q-fb.ok{background:var(--sage-soft);color:var(--sage-ink)}
.q-fb.no{background:var(--peach-soft);color:#BE185D}
.q-fb svg{width:17px;height:17px;margin-top:2px}
.q-actions{display:flex;justify-content:flex-end;margin-top:20px}
.q-result{text-align:center;padding:26px 0 10px}
.q-score{font-family:'Fraunces',serif;font-size:4.2rem;font-weight:560;line-height:1;display:block;color:var(--primary)}
.q-score span{font-size:1.8rem;color:var(--faint)}
.q-msg{font-family:'Fraunces',serif;font-style:italic;font-size:1.2rem;margin-top:16px;color:var(--plum)}
.q-note{font-size:.84rem;color:var(--cocoa);margin-top:10px}
.q-result .btn{margin-top:24px}

/* ============ Wellness ============ */
.ring-wrap{display:flex;align-items:center;gap:16px;justify-self:end;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:13px 20px;box-shadow:0 18px 40px -25px rgba(108,92,231,.3)}
.ring-wrap svg{width:66px;height:66px}
.ring-lab b{font-family:'Fraunces',serif;font-size:1.1rem;display:block;line-height:1.2;color:var(--plum)}
.ring-lab span{font-size:.78rem;color:var(--cocoa);font-weight:700}
.vgrid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.vcard{background:var(--card);border:1px solid var(--line);border-radius:24px;overflow:hidden;cursor:pointer;transition:transform .35s ease,box-shadow .35s ease,border-color .3s}
.vcard:hover{transform:translateY(-7px);box-shadow:0 26px 50px -28px rgba(108,92,231,.35);border-color:var(--primary)}
.v-thumb{position:relative;aspect-ratio:16/11;overflow:hidden}
.v-play{position:absolute;inset:0;margin:auto;width:54px;height:54px;border-radius:50%;background:rgba(255,255,255,.94);display:grid;place-items:center;color:var(--primary);transition:transform .3s;z-index:2;box-shadow:0 4px 14px rgba(0,0,0,.15)}
.v-play svg{width:20px;height:20px}
.vcard:hover .v-play{transform:scale(1.14)}
.v-dur{position:absolute;bottom:10px;right:10px;background:rgba(20,14,54,.78);color:#F3EEFA;font-size:.7rem;font-weight:800;padding:4px 11px;border-radius:999px;z-index:2}
.v-done{position:absolute;top:10px;left:10px;background:var(--sage);color:#FFFFFF;font-size:.66rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;padding:5px 11px;border-radius:999px;display:none;align-items:center;gap:6px;z-index:2}
.v-done svg{width:12px;height:12px}
.vcard.done .v-done{display:inline-flex}
.v-body{padding:15px 17px 18px}
.v-meta{display:flex;gap:9px;align-items:center;margin-bottom:8px}
.pill-lvl{background:var(--primary-soft);color:var(--primary);font-size:.64rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;padding:4px 10px;border-radius:999px}
.v-tri{font-size:.76rem;color:var(--cocoa);font-weight:700}
.v-body h3{font-size:1.06rem;font-weight:560;line-height:1.3;color:var(--plum)}
@media(max-width:1020px){.vgrid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.vgrid{grid-template-columns:1fr}}

/* ============ Modal ============ */
.modal{position:fixed;inset:0;z-index:1500;display:grid;place-items:center;padding:20px}
.modal[hidden]{display:none}
.modal-back{position:absolute;inset:0;background:rgba(20,14,54,.65);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);opacity:0;transition:.3s}
.modal-card{position:relative;background:var(--card);border-radius:26px;max-width:540px;width:100%;max-height:88vh;overflow:auto;transform:translateY(18px) scale(.97);opacity:0;transition:.35s cubic-bezier(.22,.8,.24,1);box-shadow:0 40px 90px -30px rgba(20,14,54,.6)}
.modal.open .modal-back{opacity:1}
.modal.open .modal-card{transform:none;opacity:1}
.modal-x{position:absolute;top:14px;right:14px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.94);display:grid;place-items:center;box-shadow:0 8px 20px -8px rgba(30,27,75,.35);z-index:3;color:var(--plum)}
.modal-x svg{width:16px;height:16px}
.vm-media{position:relative;height:212px;overflow:hidden}
.vm-media::after{content:"";position:absolute;inset:0;background:rgba(20,14,54,.14);z-index:1}
#vmArt{position:absolute;inset:0}
#vmArt>svg{position:absolute;inset:0;width:100%;height:100%}
.vm-dur{position:absolute;bottom:12px;left:14px;background:rgba(20,14,54,.78);color:#F3EEFA;font-size:.72rem;font-weight:800;padding:5px 12px;border-radius:999px;z-index:2}
.vm-body{padding:22px 26px 26px}
.vm-meta{display:flex;gap:9px;align-items:center;margin-bottom:9px}
.vm-body h3{font-size:1.35rem;font-weight:560;color:var(--plum)}
.vm-body>p{color:var(--cocoa);font-size:.93rem;margin-top:9px}
.vm-list{margin-top:14px}
.vm-list li{display:flex;gap:10px;align-items:flex-start;padding:9px 0;border-top:1px dashed var(--line);font-size:.9rem}
.vm-list svg{width:15px;height:15px;color:var(--primary);margin-top:3px}
.vm-caution{display:flex;gap:10px;background:rgba(245,158,11,.12);color:var(--honey-ink);padding:12px 14px;border-radius:12px;font-size:.84rem;margin-top:14px;font-weight:600}
.vm-caution svg{width:16px;height:16px;margin-top:2px;color:var(--honey)}
#vmDone{width:100%;margin-top:18px}
#vmDone .d-in{display:none;align-items:center;gap:8px}
#vmDone.is-done{background:var(--sage);pointer-events:none}
#vmDone.is-done .d-go{display:none}
#vmDone.is-done .d-in{display:inline-flex}
body.modal-open{overflow:hidden}

/* ============ Care Circle (deep) ============ */
.chain-wrap{margin-block:6px clamp(44px,6vw,68px)}
.chain-head{display:flex;justify-content:flex-end;margin-bottom:18px}
.live{display:inline-flex;align-items:center;gap:9px;font-size:.66rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:rgba(237,233,254,.65)}
.live .live-dot{background:var(--peach)}
.chain{position:relative;display:flex;gap:8px;padding:4px 0}
.chain-node{flex:1;display:flex;flex-direction:column;align-items:center;text-align:center;gap:11px;min-width:0}
.chain-ico{width:58px;height:58px;border-radius:50%;border:1px solid rgba(237,233,254,.25);display:grid;place-items:center;color:#F3EEFA;background:rgba(237,233,254,.06);transition:all .45s ease}
.chain-ico svg{width:22px;height:22px}
.chain-node.lit .chain-ico{border-color:var(--peach);color:#FFFFFF;transform:scale(1.09);background:rgba(236,72,153,.25);box-shadow:0 0 20px rgba(236,72,153,.4)}
.chain-name{font-weight:800;font-size:.92rem;color:#FFFFFF}
.chain-cap{font-size:.76rem;color:rgba(237,233,254,.65);line-height:1.5;max-width:19ch}
.chain-line{position:absolute;background:rgba(237,233,254,.16)}
.chain-dot{position:absolute;top:0;left:0;width:11px;height:11px;margin:-5.5px 0 0 -5.5px;border-radius:50%;background:var(--peach);z-index:2;box-shadow:0 0 10px var(--peach)}
.comm-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:clamp(24px,3vw,44px);padding-top:clamp(30px,4vw,42px);border-top:1px solid rgba(237,233,254,.18);margin-bottom:clamp(46px,6vw,70px)}
.comm-stats b{font-family:'Fraunces',serif;font-size:clamp(2.6rem,4vw,3.4rem);font-weight:560;line-height:1;display:block;color:var(--peach)}
.comm-stats b sup{font-size:.45em;font-weight:500}
.comm-stats span{display:block;font-size:.85rem;color:rgba(237,233,254,.75);margin-top:10px;line-height:1.55;max-width:30ch}
.t-h{font-size:.7rem;letter-spacing:.24em;text-transform:uppercase;font-weight:800;color:rgba(237,233,254,.6);margin-bottom:22px}
.t-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.t-card{background:rgba(237,233,254,.05);border:1px solid rgba(237,233,254,.14);border-radius:24px;overflow:hidden;transition:transform .35s ease,border-color .35s ease}
.t-card:hover{transform:translateY(-6px);border-color:rgba(236,72,153,.4)}
.t-img{height:200px}
.t-body{padding:20px 22px 24px}
.t-body p{font-family:'Fraunces',serif;font-style:italic;font-size:1.05rem;line-height:1.55;color:#FFFFFF}
.t-body b{display:block;font-size:.88rem;margin-top:14px;color:var(--peach)}
.t-body span{font-size:.78rem;color:rgba(237,233,254,.65);font-weight:700}
@media(max-width:780px){
  .chain{flex-direction:column;gap:26px}
  .chain-node{flex-direction:row;align-items:flex-start;text-align:left;gap:18px}
  .chain-cap{max-width:320px}
  .comm-stats{grid-template-columns:1fr;gap:28px}
  .t-grid{grid-template-columns:1fr}
}

/* ============ Ask ============ */
.consult-grid{display:grid;grid-template-columns:minmax(0,.88fr) minmax(0,1.12fr);gap:clamp(32px,5vw,68px);align-items:start}
.consult-visual{position:relative}
.consult-img{border-radius:999px 999px 28px 28px;overflow:hidden;aspect-ratio:4/5;box-shadow:0 36px 70px -40px rgba(108,92,231,.35)}
.c-chip{position:absolute;bottom:26px;left:-16px;background:var(--card);border-radius:999px;padding:11px 18px;display:flex;align-items:center;gap:11px;box-shadow:0 18px 40px -18px rgba(30,27,75,.35);z-index:2;border:1px solid var(--line)}
.c-chip svg{width:18px;height:18px;color:var(--primary)}
.c-chip b{display:block;font-size:.86rem;color:var(--plum)}
.c-chip span{font-size:.72rem;color:var(--cocoa);font-weight:700}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.f-field{display:flex;flex-direction:column;gap:7px;margin-bottom:15px}
.f-field label{font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--cocoa)}
.f-field input,.f-field select,.f-field textarea{border:1px solid var(--line-strong);border-radius:14px;padding:13px 16px;font:inherit;font-size:.95rem;background:var(--cream);color:var(--plum);outline:none;transition:border-color .25s,box-shadow .25s;width:100%}
.f-field input:focus,.f-field select:focus,.f-field textarea:focus{border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-soft)}
.f-field textarea{min-height:110px;resize:vertical}
.sel{position:relative}
.sel select{appearance:none;-webkit-appearance:none;padding-right:46px}
.sel .chev{position:absolute;right:16px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--cocoa)}
.ferr{display:none;align-items:center;gap:6px;font-size:.77rem;color:#DC2626;font-weight:800}
.ferr.on{display:flex}
.ferr svg{width:13px;height:13px}
.ask-card{background:var(--card);border:1px solid var(--line);border-radius:24px;padding:clamp(20px,2.6vw,28px);box-shadow:0 24px 50px -32px rgba(108,92,231,.25)}
.ask-ok{display:flex;align-items:center;justify-content:center;gap:16px;padding:20px 0}
.ask-ok .ok-ic{width:52px;height:52px;border-radius:50%;background:var(--sage-soft);color:var(--sage-ink);display:grid;place-items:center;flex:none}
.ask-ok .ok-ic svg{width:24px;height:24px}
.ask-ok b{display:block;font-size:1.1rem;color:var(--plum)}
.ask-ok span{font-size:.88rem;color:var(--cocoa)}
.faq{margin-top:28px}
.faq-h{font-size:.72rem;letter-spacing:.2em;text-transform:uppercase;font-weight:800;color:var(--faint);margin-bottom:4px}
.acc-row{border-bottom:1px solid var(--line)}
.faq .acc-row:first-of-type{border-top:1px solid var(--line)}
.faq-q{width:100%;display:flex;justify-content:space-between;align-items:center;gap:14px;text-align:left;padding:16px 4px;font-weight:800;font-size:.95rem;color:var(--plum)}
.acc-chev{width:34px;height:34px;border:1px solid var(--line-strong);border-radius:50%;display:grid;place-items:center;transition:transform .4s ease,border-color .3s,color .3s;flex:none;color:var(--cocoa)}
.acc-chev svg{width:14px;height:14px}
.acc-row.open .acc-chev{transform:rotate(180deg);border-color:var(--primary);color:var(--primary)}
.acc-body{display:grid;grid-template-rows:0fr;transition:grid-template-rows .5s cubic-bezier(.22,.8,.24,1)}
.acc-row.open .acc-body{grid-template-rows:1fr}
.acc-inner{overflow:hidden}
.acc-inner p{color:var(--cocoa);font-size:.9rem;padding:0 4px 18px;max-width:60ch}
@media(max-width:1020px){.consult-grid{grid-template-columns:1fr}.consult-visual{max-width:440px;margin-inline:auto;width:100%}}
@media(max-width:560px){.f-row{grid-template-columns:1fr}}

/* ============ Footer ============ */
footer{background:var(--blush);border-top:1px solid var(--line);padding:clamp(48px,6vw,68px) 0 26px}
.foot-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:clamp(28px,4vw,56px)}
.foot-brand p{font-size:.85rem;color:var(--cocoa);margin-top:14px;max-width:36ch;line-height:1.6}
.foot-col h4{font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;color:var(--faint);margin-bottom:16px;font-weight:800}
.foot-col a,.foot-col li{display:block;font-size:.9rem;color:var(--cocoa);padding:4px 0;font-weight:700;transition:color .2s}
.foot-col a:hover{color:var(--primary)}
.foot-urgent{display:flex;gap:13px;align-items:flex-start;background:var(--card);border:1px solid var(--line);border-left:4px solid var(--primary);border-radius:16px;padding:16px 20px;margin-top:clamp(30px,4vw,40px)}
.foot-urgent svg{width:18px;height:18px;color:var(--primary);margin-top:3px}
.foot-urgent b{color:var(--primary-deep)}
.foot-urgent p{font-size:.85rem;color:var(--cocoa)}
.foot-bottom{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;border-top:1px solid var(--line);margin-top:clamp(36px,5vw,52px);padding-top:22px;font-size:.78rem;color:var(--cocoa);font-weight:700}
@media(max-width:780px){.foot-grid{grid-template-columns:1fr}}

/* ============ Toasts / reveal ============ */
.toasts{position:fixed;bottom:24px;right:24px;z-index:3000;display:flex;flex-direction:column;gap:10px}
.toast{display:flex;align-items:center;gap:11px;background:var(--plum-deep);color:#F3EEFA;padding:14px 18px;border-radius:13px;font-size:.88rem;font-weight:700;max-width:340px;box-shadow:0 18px 40px -16px rgba(20,14,54,.5);transform:translateY(16px);opacity:0;transition:.4s cubic-bezier(.22,.8,.24,1);border:1px solid rgba(255,255,255,.1)}
.toast.show{transform:none;opacity:1}
.toast svg{width:17px;height:17px;color:var(--peach)}
.reveal{opacity:0;transform:translateY(26px);transition:opacity .8s ease,transform .8s cubic-bezier(.22,.8,.24,1);transition-delay:var(--d,0s)}
.reveal.in{opacity:1;transform:none}

@media(max-width:1020px){
  .sec-head{grid-template-columns:1fr;gap:20px}
  .track-grid{grid-template-columns:1fr}
  .ring-wrap{justify-self:start}
}
@media(max-width:560px){
  .hero-cta .btn{width:100%}
  .hero-stats div+div{border-left:0;padding-left:0}
  .appts li{grid-template-columns:92px 1fr}
  .ap-l{grid-column:2}
  .toasts{left:20px;right:20px}
}
@media(prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.001s !important;transition-duration:.001s !important}
  .mq-track{animation:none}
}
</style>
</head>
<body>

<!-- ======== Announcement ======== -->
<div class="announce"><i data-lucide="heart"></i>Free for every expectant mother · in Filipino &amp; English · Brought to your Barangay Health Station</div>

<!-- ======== Nav ======== -->
<nav class="nav" id="nav">
  <div class="wrap nav-inner">
    <a class="brand" href="#top" aria-label="ReproCare home">
      <img src="{{ asset('images/brand/reprocare-logo.svg') }}" alt="ReproCare Logo" width="32" height="32" onerror="this.style.display='none'">
      ReproCare
    </a>
    <div class="nav-links" id="navLinks">
      <a href="#baby">Your baby</a>
      <a href="#tracking">Your numbers</a>
      <a href="#classes">Classes</a>
      <a href="#wellness">Wellness</a>
      <a href="#community">Care circle</a>
      <a href="#ask" class="nav-book">Ask a midwife</a>
      <a href="#apptCard" class="nav-book">Book a check-up</a>
    </div>
    <div class="nav-actions">
      @auth
        <a class="btn btn-primary sm" href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      @else
        <a class="btn btn-ghost sm" href="{{ route('login') }}"><i data-lucide="log-in"></i> Sign In</a>
        <a class="btn btn-primary sm btn-book-nav" id="navBook" href="#apptCard">Book check-up</a>
      @endauth
    </div>
    <button class="menu-btn" id="menuBtn" aria-label="Toggle menu">
      <i data-lucide="menu" class="ic-menu"></i><i data-lucide="x" class="ic-x"></i>
    </button>
  </div>
</nav>

<!-- ======== HERO ======== -->
<header class="hero" id="top">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <p class="eyebrow reveal"><span class="live-dot"></span>ReproCare · Maternal &amp; Reproductive Health System</p>
      <h1 class="reveal" style="--d:.08s">Take your first steps on the <em>pregnancy path.</em></h1>
      <p class="hero-sub reveal" style="--d:.16s">From the first heartbeat to the first cry — your baby's week-by-week growth, your health numbers, free learning modules, and a dedicated care team in your barangay. All in one safe, warm record.</p>
      <div class="hero-cta reveal" style="--d:.24s">
        @auth
          <a class="btn btn-primary" href="{{ route('dashboard') }}">Go to Dashboard <i data-lucide="arrow-right"></i></a>
        @else
          <a class="btn btn-primary" href="{{ route('login') }}">Access Patient Portal <i data-lucide="arrow-right"></i></a>
        @endauth
        <a class="btn btn-ghost" id="heroBook" href="#apptCard">Book a check-up <i data-lucide="calendar-check"></i></a>
        <a class="btn btn-ghost" href="#baby">Meet your baby <i data-lucide="arrow-down"></i></a>
      </div>
      <div class="hero-stats reveal" style="--d:.32s">
        <div><b>40</b><span>weeks, guided with you</span></div>
        <div><b>8</b><span>check-ups, scheduled per DOH</span></div>
        <div><b>30+</b><span>free classes &amp; lessons</span></div>
      </div>
    </div>

    <div class="hero-visual reveal" style="--d:.18s">
      <span class="arch-outline" aria-hidden="true"></span>
      <div class="hero-photo zoomable" data-art="hero"></div>
      <div class="float-chip" id="heroChip">
        <i data-lucide="heart-pulse"></i>
        <div><b id="hcBpm">146</b><span id="hcSub">bpm · week 24 · steady</span></div>
      </div>

      <div class="appt-card" id="apptCard">
        <div class="ap-head">
          <span class="ap-ic"><i data-lucide="calendar"></i></span>
          <div><b>Schedule an Appointment</b><span>Prenatal check-up · Barangay health station</span></div>
        </div>

        @guest
          <div class="ap-auth-notice" style="background: rgba(108,92,231,0.06); border: 1px solid rgba(108,92,231,0.18); border-radius: 16px; padding: 16px; margin: 14px 0 16px;">
            <div style="display: flex; gap: 11px; align-items: flex-start;">
              <span style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); display: grid; place-items: center; flex-shrink: 0; margin-top: 1px;">
                <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
              </span>
              <div>
                <b style="font-size: 0.94rem; color: var(--plum); display: block; margin-bottom: 3px; font-weight: 700;">Sign in required to schedule</b>
                <p style="font-size: 0.82rem; color: var(--cocoa); margin: 0; line-height: 1.5;">To book an official prenatal check-up and connect it with your Barangay Health Worker (BHW) and Midwife, please sign in. If you don't have an account, you'll need to create one first.</p>
              </div>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%;"><i data-lucide="log-in"></i> Sign In to Book Check-up</a>
            <a href="{{ route('register') }}" class="btn btn-ghost" style="width: 100%; font-size: 0.88rem;"><i data-lucide="user-plus"></i> Create an Account</a>
          </div>
          <p class="ap-fine" style="margin-top: 14px;"><i data-lucide="info"></i>Walk-in consultations are also welcomed at your Barangay Health Station.</p>
        @else
          <div id="apForm">
            <label for="apDate">Preferred date</label>
            <input type="date" id="apDate">
            <label>Preferred time</label>
            <div class="ap-times">
              <button type="button" class="ap-time" data-t="9:00 AM">9:00 AM</button>
              <button type="button" class="ap-time" data-t="10:30 AM">10:30</button>
              <button type="button" class="ap-time" data-t="1:00 PM">1:00 PM</button>
              <button type="button" class="ap-time" data-t="3:30 PM">3:30</button>
            </div>
            <p class="ap-err" id="apErr" hidden><i data-lucide="alert-triangle"></i><span id="apErrTxt"></span></p>
            <button class="btn btn-primary" id="apBook" type="button">Book check-up <i data-lucide="arrow-right"></i></button>
            <p class="ap-fine"><i data-lucide="info"></i>SMS confirmation &amp; reminders will be sent to your registered number.</p>
          </div>
          <div class="ap-done" id="apDone" hidden>
            <span class="ok-big"><i data-lucide="check"></i></span>
            <b>You're booked.</b>
            <p class="ap-when" id="apWhen"></p>
            <p class="ap-where" id="apAt"></p>
            <div class="ap-done-btns">
              <button class="btn btn-ghost sm" id="apRemind" type="button">Add SMS alert</button>
              <button class="link-btn" id="apAgain" type="button">Book another</button>
            </div>
          </div>
        @endguest
      </div>
    </div>
  </div>
</header>

<!-- ======== Marquee ======== -->
<div class="marquee" aria-hidden="true">
  <div class="mq-track" id="mqTrack">
    <span class="mq-item"><i data-lucide="heart"></i>Sleep on your left side with a pillow between knees</span>
    <span class="mq-item"><i data-lucide="heart"></i>Folic acid daily, from pre-conception through first trimester</span>
    <span class="mq-item"><i data-lucide="heart"></i>Eight prenatal visits — following DOH &amp; WHO protocols</span>
    <span class="mq-item"><i data-lucide="heart"></i>Count ten kicks once a day starting at week 28</span>
    <span class="mq-item"><i data-lucide="heart"></i>Unang Yakap: immediate skin-to-skin within the first hour</span>
    <span class="mq-item"><i data-lucide="heart"></i>Hydration first — water always within easy reach</span>
    <span class="mq-item"><i data-lucide="heart"></i>Ask your BHW &amp; midwife — no question is too small</span>
    <span class="mq-item"><i data-lucide="heart"></i>Screen for gestational diabetes at weeks 24–28</span>
  </div>
</div>

<!-- ======== CATEGORIES ======== -->
<section class="sec" id="categories">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Explore System</span></div>
        <h2>Everything your pregnancy needs, <em>in one warm place.</em></h2>
      </div>
      <p class="sec-lede reveal" style="--d:.1s">Five core modules designed for expectant mothers, health workers, and your entire community support network.</p>
    </div>
    <div class="cat-grid">
      <a class="cat reveal" href="#baby">
        <div class="cat-img zoomable" data-art="cat-baby"></div>
        <div class="cat-t"><b>Meet Your Baby</b><span class="cat-go"><i data-lucide="arrow-up-right"></i></span></div>
        <span class="cat-s">Week-by-week growth tracker</span>
      </a>
      <a class="cat reveal" href="#tracking" style="--d:.07s">
        <div class="cat-img zoomable" data-art="cat-numbers"></div>
        <div class="cat-t"><b>Your Numbers</b><span class="cat-go"><i data-lucide="arrow-up-right"></i></span></div>
        <span class="cat-s">Weight, visits &amp; SMS alerts</span>
      </a>
      <a class="cat reveal" href="#classes" style="--d:.14s">
        <div class="cat-img zoomable" data-art="cat-classes"></div>
        <div class="cat-t"><b>Free Classes</b><span class="cat-go"><i data-lucide="arrow-up-right"></i></span></div>
        <span class="cat-s">Lessons, quizzes &amp; guides</span>
      </a>
      <a class="cat reveal" href="#wellness" style="--d:.21s">
        <div class="cat-img zoomable" data-art="cat-move"></div>
        <div class="cat-t"><b>Gentle Movement</b><span class="cat-go"><i data-lucide="arrow-up-right"></i></span></div>
        <span class="cat-s">Prenatal yoga &amp; breathing</span>
      </a>
      <a class="cat reveal" href="#community" style="--d:.28s">
        <div class="cat-img zoomable" data-art="cat-circle"></div>
        <div class="cat-t"><b>Your Care Circle</b><span class="cat-go"><i data-lucide="arrow-up-right"></i></span></div>
        <span class="cat-s">BHW, Midwife, RHU &amp; CHO</span>
      </a>
    </div>
  </div>
</section>

<!-- ======== MEET YOUR BABY ======== -->
<section class="sec tint" id="baby">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Fetal Development</span></div>
        <h2>Meet your baby, <em>week by week.</em></h2>
      </div>
      <p class="sec-lede reveal" style="--d:.1s">Drag through 40 weeks of miraculous growing — realistic dimensions, weights, heartbeats, and maternal tips. Set your due date below to track along in real time.</p>
    </div>

    <div class="calc-row reveal" style="background: var(--card); border: 1px solid var(--line); border-radius: 20px; padding: 20px 24px; gap: 16px; margin-bottom: 28px; box-shadow: 0 10px 24px -10px rgba(108,92,231,0.12); flex-wrap: wrap;">
      <div style="flex: 1; min-width: 200px;">
        <span class="calc-lab" style="font-size: 0.95rem; font-weight: 800; color: var(--plum); display: block; margin-bottom: 3px;">First Day of Your Last Period (LMP)</span>
        <span style="font-size: 0.78rem; color: var(--cocoa); font-weight: 600;">Enter the date your last menstrual period started to calculate your current week</span>
      </div>
      <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
        <div style="display: flex; flex-direction: column; gap: 4px;">
          <label for="dueIn" style="font-size: 0.68rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--cocoa);">Date of Last Period</label>
          <input type="date" class="calc-in" id="dueIn" aria-label="First day of last menstrual period" style="border-radius: 12px; min-width: 160px;">
        </div>
        <button class="btn btn-primary sm" id="dueBtn" type="button" style="margin-top: 18px;"><i data-lucide="calculator"></i> Calculate Week</button>
      </div>
      <div id="dueResult" style="width: 100%; display: none; margin-top: 4px; padding: 14px 18px; background: var(--primary-soft); border: 1px solid rgba(108,92,231,0.2); border-radius: 14px;">
        <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <span style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: #fff; display: grid; place-items: center; flex-shrink: 0;">
              <i data-lucide="baby" style="width: 18px; height: 18px;"></i>
            </span>
            <div>
              <span style="font-size: 0.68rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--primary-deep); display: block;">Current Week</span>
              <span id="dueChipTxt" style="font-size: 1rem; font-weight: 800; color: var(--plum);">—</span>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <span style="width: 38px; height: 38px; border-radius: 50%; background: rgba(236,72,153,0.12); color: var(--primary); display: grid; place-items: center; flex-shrink: 0;">
              <i data-lucide="calendar-heart" style="width: 18px; height: 18px;"></i>
            </span>
            <div>
              <span style="font-size: 0.68rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--primary-deep); display: block;">Estimated Due Date</span>
              <span id="dueEddTxt" style="font-size: 1rem; font-weight: 800; color: var(--plum);">—</span>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 10px;">
            <span style="width: 38px; height: 38px; border-radius: 50%; background: rgba(16,185,129,0.12); color: #10B981; display: grid; place-items: center; flex-shrink: 0;">
              <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
            </span>
            <div>
              <span style="font-size: 0.68rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--primary-deep); display: block;">Days Remaining</span>
              <span id="dueDaysLeft" style="font-size: 1rem; font-weight: 800; color: var(--plum);">—</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="jcard reveal" style="--d:.08s" id="jcard">
      <div class="j-stage">
        <span class="st-chip" id="stChip"><i data-lucide="heart-pulse"></i><div><b id="stBpm">146</b><span>beats/min</span></div></span>
        <svg class="baby" viewBox="0 0 240 220" preserveAspectRatio="xMidYMax meet" aria-hidden="true">
          <ellipse cx="120" cy="200" rx="80" ry="11" fill="rgba(108,92,231,.18)"/>
          <g id="babyG" style="transform:scale(1)">
            <g id="babyInner">
              <ellipse cx="120" cy="150" rx="54" ry="48" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="3"/>
              <path d="M158 148 C150 134 138 129 127 135" stroke="#1E1B4B" stroke-width="3" fill="none" stroke-linecap="round"/>
              <circle cx="120" cy="88" r="41" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="3"/>
              <path d="M112 49 q9 -14 21 -6" stroke="#1E1B4B" stroke-width="3" fill="none" stroke-linecap="round"/>
              <path d="M98 88 q6 6 12 0" stroke="#1E1B4B" stroke-width="2.5" fill="none" stroke-linecap="round"/>
              <path d="M128 88 q6 6 12 0" stroke="#1E1B4B" stroke-width="2.5" fill="none" stroke-linecap="round"/>
              <path d="M114 104 q6 5 12 0" stroke="#1E1B4B" stroke-width="2.5" fill="none" stroke-linecap="round"/>
              <circle cx="96" cy="101" r="5.5" fill="rgba(236,72,153,.35)"/>
              <circle cx="144" cy="101" r="5.5" fill="rgba(236,72,153,.35)"/>
            </g>
          </g>
        </svg>
      </div>

      <div class="j-info">
        <div class="j-week">
          <b id="jWeekB">24</b><span>of 40 weeks</span>
          <span class="j-tri" id="jTri">Second trimester</span>
        </div>
        <p class="j-size" id="jSize">about the size of an ear of corn</p>
        <p class="j-note" id="jNote">Lungs are branching out airways; gestational diabetes screening window opens.</p>
        <p class="j-tip"><i data-lucide="heart"></i><span id="jTip">Take the Oral Glucose Tolerance Test (OGTT) between weeks 24–28.</span></p>
        <div class="j-stats">
          <div class="j-stat"><i data-lucide="ruler"></i><div><b id="jLen">30</b><span>length</span></div></div>
          <div class="j-stat"><i data-lucide="weight"></i><div><b id="jWt">600</b><span>weight</span></div></div>
          <div class="j-stat" id="jBpmBox"><i data-lucide="heart-pulse"></i><div><b id="jBpm">146</b><span>heart rate</span></div></div>
        </div>
      </div>

      <div class="scrub">
        <p class="scrub-hint"><i data-lucide="move-horizontal"></i>Drag through the weeks</p>
        <div class="scrub-track" id="scrubTrack">
          <div class="scrub-line"></div>
          <div class="scrub-fill" id="scrubFill"></div>
          <span class="scrub-tick" style="left:0%"><i></i><em>1</em></span>
          <span class="scrub-tick" style="left:30.8%"><i></i><em>13</em></span>
          <span class="scrub-tick" style="left:69.2%"><i></i><em>28</em></span>
          <span class="scrub-tick" style="left:100%"><i></i><em>40</em></span>
          <button class="scrub-handle" id="scrubHandle" role="slider" aria-valuemin="1" aria-valuemax="40" aria-valuenow="24" aria-label="Gestational week"><span id="scrubWk">24</span></button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======== YOUR NUMBERS ======== -->
<section class="sec" id="tracking">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Health Records</span></div>
        <h2>Your numbers, <em>gently watched.</em></h2>
      </div>
      <p class="sec-lede reveal" style="--d:.1s">Weight gain trajectories, prenatal visits, and health worker alerts — logged in seconds, validated against clinical safety bands, and accessible to your care team.</p>
    </div>

    <div class="track-grid">
      <div class="chart-card reveal" id="chartCard">
        <div class="chart-head">
          <h3>Your weight gain, week by week</h3>
          <span class="chart-sub">Maternal record · weeks 8–40</span>
        </div>
        <div class="chart-wrap">
          <svg id="wChart" viewBox="0 0 640 380"></svg>
          <div class="tt" id="wTt"></div>
        </div>
        <div class="legend">
          <span class="lg"><i class="sw sw-band"></i>Expected healthy range</span>
          <span class="lg"><i class="sw sw-line"></i>Your recorded line</span>
          <span class="lg"><i class="sw sw-flag"></i>Flagged consultation reading</span>
        </div>
        <div class="log-row">
          <label for="logGain">Log weight gain for current week:</label>
          <input type="number" id="logGain" min="0" max="40" step="0.1" placeholder="e.g. 8.5" aria-label="Weight gained in kilograms">
          <span style="font-weight:800;color:var(--cocoa);font-size:.85rem">kg</span>
          <button class="btn btn-primary sm" id="logBtn" type="button">Log Weight <i data-lucide="check"></i></button>
          <p class="log-msg" id="logMsg"></p>
        </div>
        <p class="chart-foot"><i data-lucide="info"></i>Hover or tap the chart to inspect readings. Everything you log syncs with your official ReproCare maternal profile.</p>
      </div>

      <div class="panel-card reveal" style="--d:.12s">
        <p class="panel-cap">Upcoming Appointments · DOH Protocol</p>
        <ul class="appts" id="apptList">
          <li><span class="ap-d">May 12 · <i>W24</i></span><span class="ap-n">Oral Glucose Tolerance Test</span><span class="ap-l">Barangay Health Station</span></li>
          <li><span class="ap-d">May 26 · <i>W28</i></span><span class="ap-n">TT2 Vaccine &amp; CBC Lab Test</span><span class="ap-l">Barangay Health Station</span></li>
          <li><span class="ap-d">Jun 23 · <i>W32</i></span><span class="ap-n">Fetal Growth &amp; Ultrasound Check</span><span class="ap-l">Rural Health Unit (RHU)</span></li>
          <li><span class="ap-d">Jul 21 · <i>W36</i></span><span class="ap-n">Weekly Term Monitoring Begins</span><span class="ap-l">Barangay Health Station</span></li>
        </ul>
        <div class="rem-row">
          <div>
            <div><b>Automated SMS Reminders</b><span>3 days before every scheduled visit</span></div>
            <button class="switch" role="switch" aria-checked="true" data-name="SMS reminders" aria-label="Toggle SMS reminders"></button>
          </div>
        </div>
        <div class="rem-row">
          <div>
            <div><b>BHW Home-Visit Notifications</b><span>Notifies your assigned Barangay Health Worker</span></div>
            <button class="switch" role="switch" aria-checked="true" data-name="Home-visit alerts" aria-label="Toggle home-visit alerts"></button>
          </div>
        </div>
        <div class="team">
          <div class="team-av">
            <span style="background:var(--sage)">BHW</span>
            <span style="background:var(--primary)">MW</span>
            <span style="background:var(--peach)">MD</span>
          </div>
          <p>Connected directly to your Barangay Health Worker, Midwife, and Rural Health Physician.</p>
        </div>
      </div>
    </div>

    <!-- Kick Counter -->
    <div class="kick-card reveal" id="kickCard">
      <div class="kick-copy">
        <h3>Counting her kicks is <em>counting on care.</em></h3>
        <p>From week 28 onwards: sit comfortably once daily and record how long 10 movements take. Most healthy babies achieve ten kicks within 20–30 minutes.</p>
        <div class="k-steps">
          <div class="k-step"><span class="k-n">1</span><p>Find a relaxing spot around the same time daily — ideally after a nutritious meal.</p></div>
          <div class="k-step"><span class="k-n">2</span><p>Tap the circle each time you feel a movement — flutter, turn, kick, or roll.</p></div>
          <div class="k-step"><span class="k-n">3</span><p>When you reach 10, the duration is recorded in your maternal diary.</p></div>
        </div>
        <p class="k-warn"><i data-lucide="alert-circle"></i>Fewer than 10 movements in 2 hours, or a sudden change in pattern? Contact your midwife or health station immediately.</p>
      </div>
      <div class="kick-wrap">
        <button class="kick-btn" id="kickBtn" type="button" aria-label="Tap each time you feel a movement">
          <svg class="ring" viewBox="0 0 196 196" aria-hidden="true">
            <circle cx="98" cy="98" r="88" fill="none" stroke="var(--blush-2)" stroke-width="12"/>
            <circle id="kickRing" cx="98" cy="98" r="88" fill="none" stroke="var(--primary)" stroke-width="12"
                    stroke-linecap="round" transform="rotate(-90 98 98)" stroke-dasharray="552.9" stroke-dashoffset="552.9"
                    style="transition:stroke-dashoffset .4s ease"/>
          </svg>
          <span class="kick-mid">
            <b id="kcNum">0</b>
            <small>of 10 kicks</small>
            <span class="done-in"><i data-lucide="check"></i><b class="t" id="kcFinal">10</b></span>
          </span>
        </button>
        <p class="kick-time" id="kcTime">00:00<span>elapsed</span></p>
        <p class="kick-stat" id="kcStat">Tap the circle when you feel a movement — timer starts on first tap.</p>
        <div class="kick-actions" id="kcActs">
          <button class="btn btn-primary sm" id="kcSave" type="button">Save to My Record</button>
          <button class="link-btn" id="kcReset" type="button">Reset Count</button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======== CLASSES ======== -->
<section class="sec tint" id="classes">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Learning Modules</span></div>
        <h2>Free classes for <em>every stage.</em></h2>
      </div>
      <div class="reveal" style="--d:.1s">
        <p class="sec-lede" style="margin-bottom:16px">Evidence-based maternal and reproductive health education in Filipino and English. Learn nutrition, danger signs, newborn essentials, and family planning.</p>
        <span class="plan-chip"><i data-lucide="book-open"></i><b id="planCount">0</b>&nbsp;classes selected in your plan</span>
      </div>
    </div>

    <div class="pgrid">
      <article class="pcard reveal" data-title="A Healthy Pregnancy">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-healthy"></div>
          <span class="tag">Essential</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>6 lessons · 25 min</span><span class="rate"><i data-lucide="star"></i>4.9</span></div>
          <h3>A Healthy Pregnancy</h3>
          <p>What happens at every prenatal visit — vital signs, lab tests, and reading your maternal record.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>

      <article class="pcard reveal" data-title="Nutrition & Micronutrients" style="--d:.07s">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-nutrition"></div>
          <span class="tag">Nutrition</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>5 lessons · 20 min</span><span class="rate"><i data-lucide="star"></i>4.8</span></div>
          <h3>Nutrition &amp; Micronutrients</h3>
          <p>Iron-folate supplements, calcium, iodine, and budget-friendly local meals for fetal development.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>

      <article class="pcard reveal" data-title="Danger Signs & Emergencies" style="--d:.14s">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-danger"></div>
          <span class="tag">Critical</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>4 lessons · 15 min</span><span class="rate"><i data-lucide="star"></i>5.0</span></div>
          <h3>Danger Signs &amp; Emergencies</h3>
          <p>Recognizing the 7 maternal danger signs and knowing when to go immediately to the hospital.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>

      <article class="pcard reveal" data-title="Newborn Care & Breastfeeding">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-newborn"></div>
          <span class="tag">Postpartum</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>6 lessons · 30 min</span><span class="rate"><i data-lucide="star"></i>4.9</span></div>
          <h3>Newborn Care &amp; Breastfeeding</h3>
          <p>Unang Yakap protocols, proper latching techniques, and newborn immunization schedules.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>

      <article class="pcard reveal" data-title="Family Planning & Birth Spacing" style="--d:.07s">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-planning"></div>
          <span class="tag">Reproductive</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>5 lessons · 20 min</span><span class="rate"><i data-lucide="star"></i>4.7</span></div>
          <h3>Family Planning &amp; Spacing</h3>
          <p>Understanding modern contraceptive methods, LAM, and healthy birth spacing for mother and child.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>

      <article class="pcard reveal" data-title="Mind & Mood" style="--d:.14s">
        <div class="pcard-img zoomable">
          <div class="art-fill" data-art="prog-mind"></div>
          <span class="tag">Mental Health</span><span class="free">Free</span>
        </div>
        <div class="pcard-body">
          <div class="p-meta"><span>4 lessons · 18 min</span><span class="rate"><i data-lucide="star"></i>4.8</span></div>
          <h3>Mind, Mood &amp; Well-Being</h3>
          <p>Managing pregnancy anxiety, baby blues, postpartum support, and community assistance.</p>
          <div class="pcard-foot">
            <button class="btn btn-primary sm start"><span class="s-go">Join class</span><span class="s-in"><i data-lucide="check"></i>In your plan</span></button>
            <button class="save" aria-label="Save for later"><i data-lucide="bookmark"></i></button>
          </div>
        </div>
      </article>
    </div>

    <!-- Interactive Knowledge Check Quiz -->
    <div class="quiz-card reveal">
      <div class="q-top">
        <span class="q-chip">Maternal Knowledge Check</span>
        <span class="q-meta">3 questions · 2 minutes</span>
      </div>
      <div id="quizBody"></div>
    </div>
  </div>
</section>

<!-- ======== WELLNESS ======== -->
<section class="sec" id="wellness">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Wellness &amp; Movement</span></div>
        <h2>Pregnancy workouts — <em>move gently, breathe deeply.</em></h2>
        <p class="sec-lede" style="margin-top:16px">Guided movement sessions developed alongside midwives. Safe, low-impact, and designed to ease discomfort, pelvic tension, and prepare you for labor.</p>
      </div>
      <div class="ring-wrap reveal" style="--d:.1s">
        <svg viewBox="0 0 72 72" aria-hidden="true">
          <circle cx="36" cy="36" r="30" stroke="var(--blush-2)" stroke-width="7" fill="none"/>
          <circle id="ringFg" cx="36" cy="36" r="30" stroke="var(--primary)" stroke-width="7" fill="none"
                  stroke-linecap="round" transform="rotate(-90 36 36)" stroke-dasharray="188.5" stroke-dashoffset="188.5"
                  style="transition:stroke-dashoffset 1s cubic-bezier(.22,.8,.24,1)"/>
          <text id="ringTxt" x="36" y="41" text-anchor="middle" style="font:800 15px 'Plus Jakarta Sans',sans-serif" fill="var(--plum)">0/4</text>
        </svg>
        <div class="ring-lab"><b>Sessions this week</b><span id="ringN">0</span>&nbsp;of 4 complete</div>
      </div>
    </div>

    <div class="vgrid">
      <article class="vcard reveal" data-v="0" tabindex="0" role="button" aria-label="Open session: First-Trimester Gentle Flow">
        <div class="v-thumb zoomable">
          <div class="art-fill" data-art="vid-flow-t"></div>
          <span class="v-play"><i data-lucide="play"></i></span>
          <span class="v-done"><i data-lucide="check"></i>Done</span>
          <span class="v-dur">18 min</span>
        </div>
        <div class="v-body">
          <div class="v-meta"><span class="pill-lvl">Gentle</span><span class="v-tri">Trimester 1</span></div>
          <h3>First-Trimester Gentle Flow</h3>
        </div>
      </article>
      <article class="vcard reveal" data-v="1" tabindex="0" role="button" aria-label="Open session: Breathing for Labor and Birth" style="--d:.07s">
        <div class="v-thumb zoomable">
          <div class="art-fill" data-art="vid-breath-t"></div>
          <span class="v-play"><i data-lucide="play"></i></span>
          <span class="v-done"><i data-lucide="check"></i>Done</span>
          <span class="v-dur">12 min</span>
        </div>
        <div class="v-body">
          <div class="v-meta"><span class="pill-lvl">All levels</span><span class="v-tri">All trimesters</span></div>
          <h3>Breathing for Labor &amp; Birth</h3>
        </div>
      </article>
      <article class="vcard reveal" data-v="2" tabindex="0" role="button" aria-label="Open session: Pelvic Floor Foundations" style="--d:.14s">
        <div class="v-thumb zoomable">
          <div class="art-fill" data-art="vid-pelvic-t"></div>
          <span class="v-play"><i data-lucide="play"></i></span>
          <span class="v-done"><i data-lucide="check"></i>Done</span>
          <span class="v-dur">10 min</span>
        </div>
        <div class="v-body">
          <div class="v-meta"><span class="pill-lvl">Gentle</span><span class="v-tri">All trimesters</span></div>
          <h3>Pelvic Floor Foundations</h3>
        </div>
      </article>
      <article class="vcard reveal" data-v="3" tabindex="0" role="button" aria-label="Open session: Postpartum Core Restore" style="--d:.21s">
        <div class="v-thumb zoomable">
          <div class="art-fill" data-art="vid-core-t"></div>
          <span class="v-play"><i data-lucide="play"></i></span>
          <span class="v-done"><i data-lucide="check"></i>Done</span>
          <span class="v-dur">15 min</span>
        </div>
        <div class="v-body">
          <div class="v-meta"><span class="pill-lvl">Gentle</span><span class="v-tri">Postpartum</span></div>
          <h3>Postpartum Core Restore</h3>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ======== CARE CIRCLE ======== -->
<section class="sec deep" id="community">
  <div class="wrap">
    <div class="sec-head">
      <div class="reveal">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Integrated Community Network</span></div>
        <h2>Care that travels with you, <em>doorstep to hospital.</em></h2>
      </div>
      <p class="sec-lede reveal" style="--d:.1s">Your digital maternal record connects your entire healthcare continuum — ensuring timely referrals and personalized care from your BHW to the delivery room.</p>
    </div>

    <div class="chain-wrap reveal">
      <div class="chain-head"><span class="live"><span class="live-dot"></span>Connected Care System · Active</span></div>
      <div class="chain" id="chain">
        <div class="chain-line" id="chainLine"></div>
        <div class="chain-dot" id="chainDot"></div>
        <div class="chain-node">
          <span class="chain-ico"><i data-lucide="home"></i></span>
          <span class="chain-name">Mother &amp; Family</span>
          <span class="chain-cap">Daily kick counts &amp; logs</span>
        </div>
        <div class="chain-node">
          <span class="chain-ico"><i data-lucide="users"></i></span>
          <span class="chain-name">Barangay Health Worker</span>
          <span class="chain-cap">Doorstep tracking &amp; alerts</span>
        </div>
        <div class="chain-node">
          <span class="chain-ico"><i data-lucide="stethoscope"></i></span>
          <span class="chain-name">Health Station / Midwife</span>
          <span class="chain-cap">Clinical prenatal exams</span>
        </div>
        <div class="chain-node">
          <span class="chain-ico"><i data-lucide="clipboard-plus"></i></span>
          <span class="chain-name">Rural Health Unit (RHU)</span>
          <span class="chain-cap">Physician &amp; laboratory</span>
        </div>
        <div class="chain-node">
          <span class="chain-ico"><i data-lucide="building-2"></i></span>
          <span class="chain-name">City Health Office / Hospital</span>
          <span class="chain-cap">Delivery &amp; emergency care</span>
        </div>
      </div>
    </div>

    <div class="comm-stats reveal">
      <div><b>8</b><span>Essential prenatal visits tracked digitally per pregnancy</span></div>
      <div><b>1<sup>st</sup></b><span>Hour Unang Yakap protocol followed for skin-to-skin newborn care</span></div>
      <div><b>100%</b><span>Free for mothers in participating local government units</span></div>
    </div>

    <p class="t-h reveal">Community Mothers Who Rely on ReproCare</p>
    <div class="t-grid">
      <div class="t-card reveal">
        <div class="t-img" data-art="testi-1"></div>
        <div class="t-body">
          <p>"The SMS reminders always reach my phone, even without internet. My BHW knew when my lab was due before I did."</p>
          <b>Maria Cristina</b><span>31 weeks · San Carlos City</span>
        </div>
      </div>
      <div class="t-card reveal" style="--d:.08s">
        <div class="t-img" data-art="testi-2"></div>
        <div class="t-body">
          <p>"The classes helped my husband understand the danger signs and how to support me during labor."</p>
          <b>Marites</b><span>Mother of two · Barangay Burgos</span>
        </div>
      </div>
      <div class="t-card reveal" style="--d:.16s">
        <div class="t-img" data-art="testi-3"></div>
        <div class="t-body">
          <p>"When my blood pressure flagged on the weight chart, the midwife followed up the next morning. I felt so safe."</p>
          <b>Alma</b><span>6 weeks postpartum</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======== ASK A MIDWIFE ======== -->
<section class="sec" id="ask">
  <div class="wrap">
    <div class="consult-grid">
      <div class="consult-visual reveal">
        <div class="consult-img zoomable" data-art="ask"></div>
        <div class="c-chip">
          <i data-lucide="clock"></i>
          <div><b>Prompt Responses</b><span>Within 1 working day</span></div>
        </div>
      </div>

      <div class="reveal" style="--d:.1s">
        <div class="kicker"><span class="k-rule"></span><span class="k-label">Midwife Consultation</span></div>
        <h2>No question is <em>too small.</em></h2>
        <p class="sec-lede" style="margin-top:14px">A sudden symptom, a worry at night, or dietary questions — send a message, and a licensed community midwife will provide guidance.</p>

        <div class="ask-card" style="margin-top:26px">
          <form id="askForm" novalidate>
            <div class="f-row">
              <div class="f-field">
                <label for="afName">Your full name</label>
                <input type="text" id="afName" placeholder="Maria Santos" autocomplete="name">
                <em class="ferr" id="errName"><i data-lucide="alert-triangle"></i>Please enter your name.</em>
              </div>
              <div class="f-field">
                <label for="afContact">Mobile number or email</label>
                <input type="text" id="afContact" placeholder="09xx xxx xxxx / email" autocomplete="email">
                <em class="ferr" id="errContact"><i data-lucide="alert-triangle"></i>Please enter a valid mobile number or email.</em>
              </div>
            </div>
            <div class="f-field">
              <label for="afTopic">Topic / Category</label>
              <div class="sel">
                <select id="afTopic">
                  <option>Pregnancy Symptoms &amp; Concerns</option>
                  <option>Appointment &amp; Lab Inquiries</option>
                  <option>Breastfeeding &amp; Newborn Care</option>
                  <option>Nutrition &amp; Supplements</option>
                  <option>General Health Inquiry</option>
                </select>
                <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
              </div>
            </div>
            <div class="f-field">
              <label for="afMsg">Your question or message</label>
              <textarea id="afMsg" placeholder="Describe how you are feeling or what you'd like to ask..."></textarea>
              <em class="ferr" id="errMsg"><i data-lucide="alert-triangle"></i>Please provide at least 10 characters so we can assist you properly.</em>
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%">Submit Question to Midwife <i data-lucide="send"></i></button>
          </form>
          <div class="ask-ok" id="askOk" hidden>
            <span class="ok-ic"><i data-lucide="check"></i></span>
            <div><b>Message submitted.</b><span>A registered midwife will review your inquiry. For acute emergencies, proceed to your nearest health station or hospital right away.</span></div>
          </div>
        </div>

        <div class="faq" data-acc>
          <p class="faq-h">Frequently Asked Questions</p>
          <div class="acc-row">
            <button class="faq-q" aria-expanded="false">Is ReproCare free for mothers?<span class="acc-chev"><i data-lucide="chevron-down"></i></span></button>
            <div class="acc-body"><div class="acc-inner"><p>Yes, ReproCare is a community maternal health system provided free of charge for expectant mothers, their families, and Barangay Health Stations.</p></div></div>
          </div>
          <div class="acc-row">
            <button class="faq-q" aria-expanded="false">Who has access to my health records?<span class="acc-chev"><i data-lucide="chevron-down"></i></span></button>
            <div class="acc-body"><div class="acc-inner"><p>Your records are confidential and accessible solely by authorized healthcare providers — your designated Barangay Health Worker, licensed Midwife, and Rural Health Physician.</p></div></div>
          </div>
          <div class="acc-row">
            <button class="faq-q" aria-expanded="false">Can fathers or partners join the classes?<span class="acc-chev"><i data-lucide="chevron-down"></i></span></button>
            <div class="acc-body"><div class="acc-inner"><p>Partners are warmly encouraged to join! Modules on Danger Signs, Newborn Essentials, and Family Planning are tailored for both parents.</p></div></div>
          </div>
          <div class="acc-row">
            <button class="faq-q" aria-expanded="false">What if I have limited mobile internet?<span class="acc-chev"><i data-lucide="chevron-down"></i></span></button>
            <div class="acc-body"><div class="acc-inner"><p>ReproCare features integrated SMS reminders for appointments, and your BHW performs in-person visits with automated record syncing at the health station.</p></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======== FOOTER ======== -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="brand" href="#top">
          <img src="{{ asset('images/brand/reprocare-logo.svg') }}" alt="ReproCare Logo" width="30" height="30" onerror="this.style.display='none'">
          ReproCare
        </a>
        <p>Maternal and Reproductive Health Tracking and Learning System for Community Well-Being.</p>
      </div>
      <div class="foot-col">
        <h4>Navigation</h4>
        <a href="#baby">Baby Growth Tracker</a>
        <a href="#tracking">Maternal Records</a>
        <a href="#classes">Learning Modules</a>
        <a href="#wellness">Gentle Movement</a>
        <a href="#ask">Ask a Midwife</a>
        <a href="{{ route('login') }}">Staff &amp; Patient Login</a>
      </div>
      <div class="foot-col">
        <h4>Healthcare Partners</h4>
        <ul>
          <li>Barangay Health Stations</li>
          <li>Licensed Community Midwives</li>
          <li>Rural Health Units (RHU)</li>
          <li>City Health Offices (CHO)</li>
        </ul>
      </div>
    </div>
    <div class="foot-urgent">
      <i data-lucide="alert-triangle"></i>
      <p><b>Emergency Medical Notice:</b> In case of severe vaginal bleeding, intense headache with vision disturbance, high fever, or cessation of fetal movement, please proceed to the nearest emergency hospital immediately.</p>
    </div>
    <div class="foot-bottom">
      <span>© <span id="yr"></span> ReproCare. All rights reserved.</span>
      <span>Dedicated to safe motherhood and community health.</span>
    </div>
  </div>
</footer>

<!-- ======== VIDEO MODAL ======== -->
<div class="modal" id="vModal" hidden>
  <div class="modal-back" data-close></div>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
    <button class="modal-x" data-close aria-label="Close"><i data-lucide="x"></i></button>
    <div class="vm-media">
      <div id="vmArt"></div>
      <span class="vm-dur" id="vmDur"></span>
    </div>
    <div class="vm-body">
      <div class="vm-meta"><span class="pill-lvl" id="vmLvl"></span><span class="v-tri" id="vmTri"></span></div>
      <h3 id="vmTitle"></h3>
      <p id="vmDesc"></p>
      <ul class="vm-list" id="vmList"></ul>
      <p class="vm-caution"><i data-lucide="alert-circle"></i>Always listen to your body. Pause and rest if you feel dizzy or short of breath.</p>
      <button class="btn btn-primary" id="vmDone">
        <span class="d-go">Mark as completed <i data-lucide="check"></i></span>
        <span class="d-in"><i data-lucide="check"></i>Completed</span>
      </button>
    </div>
  </div>
</div>

<div class="toasts" id="toasts" aria-live="polite"></div>

<script>
/* ================= Utilities ================= */
const icons = () => window.lucide && lucide.createIcons();
const $ = s => document.querySelector(s);
const $$ = s => [...document.querySelectorAll(s)];
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

function toast(msg, icon = 'check'){
  const t = document.createElement('div');
  t.className = 'toast';
  t.innerHTML = `<i data-lucide="${icon}"></i><span>${msg}</span>`;
  $('#toasts').appendChild(t);
  icons();
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 450); }, 3800);
}

/* =========================================================
   ILLUSTRATION KIT — Hand-drawn Vector Art with ReproCare Palette
   ========================================================= */
const ART = (function(){
  const P = (d,f,o=1)=>`<path d="${d}" fill="${f}" opacity="${o}"/>`;
  const L = (d,w,c,o=1)=>`<path d="${d}" fill="none" stroke="${c}" stroke-width="${w}" stroke-linecap="round" stroke-linejoin="round" opacity="${o}"/>`;
  const E = (cx,cy,rx,ry,f,o=1)=>`<ellipse cx="${cx}" cy="${cy}" rx="${rx}" ry="${ry}" fill="${f}" opacity="${o}"/>`;
  const C = (cx,cy,r,f,o=1)=>`<circle cx="${cx}" cy="${cy}" r="${r}" fill="${f}" opacity="${o}"/>`;
  const heart=(x,y,s,c,o=1)=>P(`M${x} ${y+s} C${x-s*1.1} ${y+s*.3} ${x-s} ${y-s*.55} ${x-s*.45} ${y-s*.55} C${x-s*.15} ${y-s*.55} ${x} ${y-s*.3} ${x} ${y-s*.1} C${x} ${y-s*.3} ${x+s*.15} ${y-s*.55} ${x+s*.45} ${y-s*.55} C${x+s} ${y-s*.55} ${x+s*1.1} ${y+s*.3} ${x} ${y+s} Z`,c,o);
  const spark=(x,y,s,c,o=.8)=>P(`M${x} ${y-s} L${x+s*.28} ${y-s*.28} L${x+s} ${y} L${x+s*.28} ${y+s*.28} L${x} ${y+s} L${x-s*.28} ${y+s*.28} L${x-s} ${y} L${x-s*.28} ${y-s*.28} Z`,c,o);
  const sprig=(x,y,s,c)=>`<g transform="translate(${x} ${y}) scale(${s})">${L('M0 0 C3 -16 -3 -34 0 -52',3,c)}${P('M1 -12 C-12 -16 -16 -28 -5 -31 C-1 -25 1 -18 1 -12 Z',c)}${P('M-1 -26 C12 -30 16 -42 5 -45 C1 -39 -1 -32 -1 -26 Z',c)}</g>`;
  const archB=(cx,yb,w,ty,c)=>{const ry=(yb-ty)*.42,x=cx-w/2;return L(`M${x} ${yb} L${x} ${ty+ry} A ${w/2} ${ry} 0 0 1 ${cx+w/2} ${ty+ry} L${cx+w/2} ${yb}`,2.5,c,.55)};
  const svgA=(label,w,h,bg,inner)=>`<svg viewBox="0 0 ${w} ${h}" role="img" aria-label="${label}" preserveAspectRatio="xMidYMid slice"><rect width="${w}" height="${h}" fill="${bg}"/>${inner}</svg>`;

  function hairBack(sty,col){
    if(sty==='long') return C(100,52,34,col)+E(63,100,13,54,col)+E(137,100,13,54,col);
    if(sty==='curly'){const pts=[[70,24],[61,42],[64,60],[130,24],[139,42],[136,60],[100,7]];return pts.map(p=>C(p[0],p[1],13,col)).join('')}
    if(sty==='braids') return E(56,128,9,52,col)+E(144,128,9,52,col)+C(56,182,7,col)+C(144,182,7,col);
    return '';
  }
  function hairFront(sty,col,wc){
    if(sty==='wrap') return P('M64 38 A36 36 0 0 1 136 38 L136 44 Q100 30 64 44 Z',wc||col)+C(134,20,7,wc||col)+C(126,14,5,wc||col);
    let s=P('M70 40 A30 30 0 0 1 130 40 L130 46 Q100 34 70 46 Z',col);
    if(sty==='bun') s+=C(100,6,13,col)+C(100,6,5,'#FFFFFF',.2);
    return s;
  }

  function mom(o){
    const S=o.skin, H=o.hairCol, CL=o.cloth, eye='#1E1B4B';
    const seated=['yoga','breathe','meditate','stretch','babylap'].includes(o.pose);
    let s=hairBack(o.hairStyle,H);
    if(o.pants){
      s+=`<rect x="82" y="176" width="16" height="62" rx="8" fill="${S}"/><rect x="102" y="176" width="16" height="62" rx="8" fill="${S}"/>`;
      s+=E(90,242,11,6,S)+E(110,242,11,6,S);
      s+=P('M100 78 C76 82 66 100 64 130 L61 182 Q100 196 139 182 L136 130 C134 100 124 82 100 78 Z',CL);
    } else if(seated){
      s+=E(100,266,68,10,'rgba(30,27,75,.08)');
      s+=P('M40 252 Q100 226 160 252 Q100 292 40 252 Z',CL)+C(46,252,12,CL)+C(154,252,12,CL);
      s+=C(68,266,8,S)+C(132,266,8,S);
      s+=P('M100 78 C74 82 62 104 58 140 C55 168 52 196 50 216 Q100 236 150 216 C148 196 145 168 142 140 C138 104 126 82 100 78 Z',CL);
    } else {
      s+=`<rect x="86" y="236" width="13" height="36" rx="6.5" fill="${S}"/><rect x="101" y="236" width="13" height="36" rx="6.5" fill="${S}"/>`;
      s+=E(90,274,11,6,S)+E(110,274,11,6,S);
      s+=P('M100 78 C72 82 60 106 56 148 C52 192 48 222 45 244 Q100 264 155 244 C152 222 148 192 144 148 C140 106 128 82 100 78 Z',CL);
    }
    const dy = seated?200:(o.pants?160:226);
    s+=C(84,dy,3,'#FFFFFF',.35)+C(116,dy+12,3,'#FFFFFF',.35)+C(100,dy+22,3,'#FFFFFF',.3);
    if(o.preg!==false && !o.pants){
      s+=E(100,seated?172:178,seated?34:38,seated?38:42,'#FFFFFF',.18);
      s+=L(`M68 ${seated?152:158} Q100 ${seated?204:212} 132 ${seated?152:158}`,2.2,'rgba(30,27,75,.28)');
    }
    const A=d=>L(d,14,S);
    switch(o.pose){
      case 'cradle':
        s+=A('M76 102 C56 130 60 170 85 197')+C(88,199,9,S);
        s+=A('M124 102 C144 130 140 170 115 197')+C(112,199,9,S);
        if(o.preg!==false) s+=heart(100,178,13,'#6C5CE7');
        break;
      case 'reach':
        s+=A('M76 102 C60 130 58 160 62 190')+C(63,194,8,S);
        s+=A('M124 102 C146 120 154 140 150 162')+C(149,166,9,S);
        break;
      case 'yoga':
        s+=A('M74 104 C56 130 48 180 50 226')+C(50,230,8,S);
        s+=A('M126 104 C144 130 152 180 150 226')+C(150,230,8,S);
        break;
      case 'stretch':
        s+=A('M74 104 C56 130 48 180 50 226')+C(50,230,8,S);
        s+=A('M126 104 C150 88 160 60 152 34')+C(151,30,8,S);
        break;
      case 'breathe':
        s+=A('M74 104 C62 130 72 152 92 166')+C(95,169,9,S);
        s+=A('M126 104 C138 130 128 152 108 166')+C(105,169,9,S);
        s+=L('M74 146 Q100 134 126 146',2,'rgba(30,27,75,.25)')+L('M66 158 Q100 144 134 158',2,'rgba(30,27,75,.15)');
        break;
      case 'meditate':
        s+=A('M74 104 C64 126 70 146 88 156')+A('M126 104 C136 126 130 146 112 156');
        s+=C(94,158,8,S)+C(106,158,8,S);
        break;
      case 'book':
        s+=A('M76 102 C60 128 66 148 82 158')+C(86,160,8,S);
        s+=A('M124 102 C140 128 134 148 118 158')+C(114,160,8,S);
        s+=P('M100 172 L62 158 L62 130 L100 144 Z','#FFFFFF')+P('M100 172 L138 158 L138 130 L100 144 Z','#FFFFFF');
        s+=P('M62 158 L62 130 L54 128 L54 156 Z',CL)+P('M138 158 L138 130 L146 128 L146 156 Z',CL);
        s+=L('M100 144 L100 172',2,'#1E1B4B')+L('M70 140 L90 146',1.6,'rgba(30,27,75,.35)')+L('M70 148 L90 154',1.6,'rgba(30,27,75,.35)')+L('M110 146 L130 140',1.6,'rgba(30,27,75,.35)')+L('M110 154 L130 148',1.6,'rgba(30,27,75,.35)');
        break;
      case 'baby':
        s+=A('M76 102 C56 126 60 150 80 170')+A('M124 102 C144 126 140 150 120 170');
        s+=`<ellipse cx="100" cy="162" rx="33" ry="25" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="2.5"/>`;
        s+=L('M74 156 Q100 166 126 154',2,'rgba(30,27,75,.22)')+L('M78 172 Q100 180 122 170',2,'rgba(30,27,75,.16)');
        s+=C(72,150,11,S)+P('M61 148 A11 11 0 0 1 83 148 Z',o.bonnCol||'#EC4899');
        s+=L('M67 150 q3 3 6 0',2,eye)+L('M73 150 q3 3 6 0',2,eye)+L('M69 156 q4 4 8 0',2,eye);
        s+=C(84,178,8,S)+C(116,178,8,S);
        s+=heart(100,112,9,'#EC4899',.9);
        break;
      case 'babylap':
        s+=A('M74 104 C58 128 62 150 82 166')+A('M126 104 C142 128 138 150 118 166');
        s+=`<ellipse cx="100" cy="206" rx="30" ry="20" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="2.5"/>`;
        s+=L('M76 202 Q100 210 124 200',2,'rgba(30,27,75,.2)');
        s+=C(74,198,10,S)+P('M64 196 A10 10 0 0 1 84 196 Z',o.bonnCol||'#F59E0B');
        s+=L('M69 198 q3 3 6 0',2,eye)+L('M75 198 q3 3 6 0',2,eye);
        s+=C(84,212,8,S)+C(116,212,8,S);
        break;
    }
    s+=`<rect x="92" y="64" width="16" height="18" rx="7" fill="${S}"/>`;
    s+=C(100,40,30,S);
    s+=hairFront(o.hairStyle,H,o.wrapCol);
    s+=C(72,50,2.5,'#F59E0B')+C(128,50,2.5,'#F59E0B');
    s+=L('M85 42 q6 6 12 0',2.6,eye)+L('M103 42 q6 6 12 0',2.6,eye);
    s+=L('M91 56 q9 9 18 0',2.6,eye);
    s+=C(80,52,5,'#EC4899',.5)+C(120,52,5,'#EC4899',.5);
    return s;
  }
  const fig=(f,x,y,s)=>`<g transform="translate(${x} ${y}) scale(${s})">${f}</g>`;

  /* Figures with ReproCare Palette */
  const mHero  = mom({skin:'#D89B78',hairCol:'#3A2A24',hairStyle:'long',cloth:'#6C5CE7',pose:'cradle'});
  const mBaby  = mom({skin:'#EDBB9F',hairCol:'#5A3A28',hairStyle:'long',cloth:'#6C5CE7',pose:'cradle'});
  const mNum   = mom({skin:'#9C6B4F',hairCol:'#241C18',hairStyle:'braids',cloth:'#10B981',pose:'cradle'});
  const mBook  = mom({skin:'#D89B78',hairCol:'#3A2A24',hairStyle:'bun',cloth:'#F59E0B',pose:'book'});
  const mYoga  = mom({skin:'#B97F5C',hairCol:'#241C18',hairStyle:'wrap',wrapCol:'#6C5CE7',cloth:'#10B981',pose:'yoga'});
  const mTrioL = mom({skin:'#9C6B4F',hairCol:'#241C18',hairStyle:'curly',cloth:'#10B981',pose:'cradle'});
  const mTrioM = mom({skin:'#EDBB9F',hairCol:'#5A3A28',hairStyle:'bun',cloth:'#6C5CE7',pose:'cradle'});
  const mTrioR = mom({skin:'#D89B78',hairCol:'#3A2A24',hairStyle:'wrap',wrapCol:'#F59E0B',cloth:'#EC4899',pose:'cradle'});
  const mNutri = mom({skin:'#B97F5C',hairCol:'#241C18',hairStyle:'bun',cloth:'#F59E0B',pose:'cradle'});
  const mDanger= mom({skin:'#D89B78',hairCol:'#241C18',hairStyle:'braids',cloth:'#EC4899',pose:'cradle'});
  const mNewb  = mom({skin:'#9C6B4F',hairCol:'#241C18',hairStyle:'wrap',wrapCol:'#10B981',cloth:'#6C5CE7',pose:'baby',bonnCol:'#10B981'});
  const mPlanM = mom({skin:'#EDBB9F',hairCol:'#5A3A28',hairStyle:'long',cloth:'#6C5CE7',pose:'cradle'});
  const mPlanP = mom({skin:'#9C6B4F',hairCol:'#241C18',hairStyle:'short',cloth:'#5E35B1',pants:true,preg:false,pose:'reach'});
  const mMind  = mom({skin:'#B97F5C',hairCol:'#241C18',hairStyle:'curly',cloth:'#EC4899',pose:'meditate'});
  const mVFlow = mom({skin:'#D89B78',hairCol:'#3A2A24',hairStyle:'long',cloth:'#6C5CE7',pose:'stretch'});
  const mVBrth = mom({skin:'#EDBB9F',hairCol:'#3A2A24',hairStyle:'wrap',wrapCol:'#EC4899',cloth:'#5E35B1',pose:'breathe'});
  const mVPelv = mom({skin:'#9C6B4F',hairCol:'#241C18',hairStyle:'bun',cloth:'#10B981',pose:'yoga'});
  const mVCore = mom({skin:'#B97F5C',hairCol:'#241C18',hairStyle:'curly',cloth:'#F59E0B',pose:'babylap',bonnCol:'#6C5CE7'});
  const mMid   = mom({skin:'#EDBB9F',hairCol:'#3A2A24',hairStyle:'bun',cloth:'#10B981',preg:false,pose:'reach'})
    + L('M86 98 C84 120 96 126 100 126 C104 126 116 120 114 98',2.5,'#1E1B4B')
    + C(100,131,6,'#6C5CE7')
    + `<rect x="70" y="146" width="19" height="14" rx="3" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="1.5"/>`
    + L('M74 151 L85 151',1.4,'#1E1B4B',.5)+L('M74 156 L82 156',1.4,'#1E1B4B',.5);
  const mT1 = mom({skin:'#EDBB9F',hairCol:'#5A3A28',hairStyle:'long',cloth:'#6C5CE7',pose:'cradle'});
  const mT2 = mom({skin:'#71483A',hairCol:'#241C18',hairStyle:'curly',cloth:'#10B981',pose:'cradle'});
  const mT3 = mom({skin:'#B97F5C',hairCol:'#3A2A24',hairStyle:'wrap',wrapCol:'#6C5CE7',cloth:'#F59E0B',pose:'cradle'});

  const vid=(label,bg,momStr,dt,dw)=>({
    thumb: svgA(label,400,275,bg, dt+fig(momStr,122,44,.78)),
    wide:  svgA(label,560,212,bg, dw+fig(momStr,198,36,.62))
  });

  const vFlow = vid('Seated pregnant mother in a gentle side stretch','#F3EEFA',mVFlow,
    spark(52,64,7,'#6C5CE7',.5)+spark(350,208,6,'#10B981',.45)+sprig(30,268,.7,'#10B981')+sprig(372,268,.65,'#10B981'),
    spark(70,56,7,'#6C5CE7',.5)+spark(500,160,6,'#10B981',.45)+heart(468,66,9,'#EC4899',.65)+sprig(44,206,.7,'#10B981')+sprig(516,206,.65,'#10B981'));
  const vBrth = vid('Seated pregnant mother breathing with hands on her bump','#EEF2EA',mVBrth,
    L('M140 62 Q200 44 260 62',2,'rgba(30,27,75,.25)')+L('M126 48 Q200 26 274 48',2,'rgba(30,27,75,.15)')+spark(52,200,5,'#10B981',.45)+spark(352,90,5,'#6C5CE7',.45),
    L('M160 46 Q260 26 360 46',2,'rgba(30,27,75,.25)')+L('M140 32 Q260 10 380 32',2,'rgba(30,27,75,.15)')+heart(470,80,9,'#6C5CE7',.6)+spark(80,160,5,'#10B981',.45));
  const vPelv = vid('Seated pregnant mother grounded, hands on knees','#FAF8FD',mVPelv,
    L('M92 260 Q200 274 308 260',2,'rgba(16,185,129,.4)')+C(200,268,4,'#10B981',.5)+sprig(34,262,.7,'#10B981')+sprig(368,262,.65,'#10B981')+spark(56,72,5,'#10B981',.5),
    L('M120 196 Q260 208 400 196',2,'rgba(16,185,129,.4)')+C(260,202,4,'#10B981',.5)+sprig(48,200,.65,'#10B981')+sprig(512,200,.6,'#10B981')+spark(90,60,5,'#10B981',.45));
  const vCore = vid('Mother with her baby resting on her lap','#F3EEFA',mVCore,
    `<g class="floaty">${heart(70,80,8,'#6C5CE7',.7)}</g><g class="floaty" style="animation-delay:1.6s">${heart(334,96,8,'#EC4899',.7)}</g>`+spark(340,210,5,'#F59E0B',.45),
    `<g class="floaty">${heart(86,58,8,'#6C5CE7',.7)}</g><g class="floaty" style="animation-delay:1.6s">${heart(478,74,8,'#EC4899',.7)}</g>`+spark(500,160,5,'#F59E0B',.45));

  return {
    'hero': svgA('A pregnant mother cradling her belly, a heart on her bump, beneath a soft arch and floating hearts',400,500,'#F3EEFA',
      C(200,116,92,'#EDE9FE',.45)
      + L('M42 150 L66 150 L74 132 L84 168 L92 150 L116 150',3,'#6C5CE7',.45)
      + archB(200,452,230,212,'rgba(108,92,231,.55)')
      + spark(332,116,7,'#6C5CE7',.6)+spark(58,224,6,'#F59E0B',.5)+spark(344,258,5,'#10B981',.5)+spark(70,90,5,'#EC4899',.6)
      + `<g class="floaty">${heart(322,148,11,'#6C5CE7',.85)}</g>`
      + `<g class="floaty" style="animation-delay:1.4s">${heart(70,206,8,'#EC4899',.8)}</g>`
      + `<g class="floaty" style="animation-delay:2.6s">${heart(338,318,7,'#10B981',.6)}</g>`
      + sprig(40,472,1.15,'#10B981')+sprig(354,472,1.05,'#10B981')
      + E(200,470,118,11,'rgba(30,27,75,.08)')
      + fig(mHero,60,50,1.4)),

    'cat-baby': svgA('Mother cradling her belly with a heart on the bump, framed by an arch',400,500,'#FAF8FD',
      archB(200,448,190,170,'rgba(108,92,231,.5)')
      + spark(318,140,6,'#6C5CE7',.5)+spark(80,180,5,'#F59E0B',.5)+spark(330,330,5,'#10B981',.45)
      + sprig(36,470,1,'#10B981')+sprig(360,470,.95,'#10B981')
      + E(200,462,100,10,'rgba(30,27,75,.07)')
      + fig(mBaby,85,128,1.15)),
    'cat-numbers': svgA('Mother with braids, a heartbeat line arcing beside her',400,500,'#F3EEFA',
      L('M30 96 L70 96 L82 72 L94 122 L106 96 L146 96',3.5,'#6C5CE7')
      + C(172,96,4.5,'#6C5CE7',.4)+C(196,96,3.5,'#6C5CE7',.28)+C(216,96,2.5,'#6C5CE7',.18)
      + archB(200,452,190,200,'rgba(16,185,129,.55)')
      + spark(330,140,6,'#10B981',.5)+spark(70,300,5,'#F59E0B',.45)
      + E(200,462,100,10,'rgba(30,27,75,.07)')
      + fig(mNum,85,128,1.15)),
    'cat-classes': svgA('Expectant mother reading an open book about her pregnancy',400,500,'#EEF2EA',
      archB(200,450,190,180,'rgba(16,185,129,.5)')
      + spark(320,130,6,'#10B981',.5)+spark(76,200,5,'#F59E0B',.5)
      + sprig(38,468,1,'#10B981')
      + E(200,460,100,10,'rgba(30,27,75,.07)')
      + fig(mBook,85,126,1.15)),
    'cat-move': svgA('Pregnant mother seated in a gentle yoga pose',400,500,'#FAF8FD',
      archB(200,430,170,225,'rgba(108,92,231,.5)')
      + L('M120 132 Q200 112 280 132',2,'rgba(30,27,75,.18)')
      + spark(330,150,6,'#6C5CE7',.5)+spark(70,220,5,'#10B981',.5)
      + sprig(40,452,1,'#10B981')+sprig(356,452,.95,'#10B981')
      + fig(mYoga,85,150,1.15)),
    'cat-circle': svgA('Three pregnant mothers standing together in community',400,500,'#F3EEFA',
      archB(200,462,150,235,'rgba(108,92,231,.5)')
      + `<g class="floaty">${heart(200,112,13,'#6C5CE7',.85)}</g>`
      + `<g class="floaty" style="animation-delay:1.2s">${heart(126,140,8,'#EC4899',.75)}</g>`
      + `<g class="floaty" style="animation-delay:2.2s">${heart(274,140,8,'#EC4899',.75)}</g>`
      + spark(60,180,6,'#F59E0B',.5)+spark(340,200,6,'#10B981',.5)
      + E(200,464,150,12,'rgba(30,27,75,.08)')
      + fig(mTrioL,14,246,.72)
      + fig(mTrioM,120,232,.8)
      + fig(mTrioR,230,246,.72)
      + L('M74 320 Q128 296 184 312',9,'#9C6B4F')
      + L('M316 320 Q268 296 216 312',9,'#D89B78')),

    'prog-healthy': svgA('Mother with a heart on her bump, framed by an arch',400,320,'#FAF8FD',
      archB(200,306,160,150,'rgba(108,92,231,.5)')
      + sprig(30,308,.85,'#10B981')+sprig(368,308,.8,'#10B981')
      + spark(56,84,6,'#6C5CE7',.5)+spark(344,110,5,'#F59E0B',.5)
      + fig(mBaby,110,40,.9)),
    'prog-nutrition': svgA('Expectant mother beside a bowl of nourishing fruits',400,320,'#FBF3EE',
      sprig(48,300,.8,'#10B981')+spark(348,84,6,'#F59E0B',.5)
      + fig(mNutri,115,24,.85)
      + P('M158 278 C158 304 174 318 200 318 C226 318 242 304 242 278 Z','#F59E0B')
      + E(200,278,42,7,'#D97706')
      + C(182,268,10,'#6C5CE7')+C(200,262,11,'#EC4899')+C(218,268,10,'#10B981')
      + sprig(200,252,.45,'#10B981')),
    'prog-danger': svgA('A pulse heart line connecting to mother',400,320,'#F3EEFA',
      heart(112,150,44,'#EC4899')
      + L('M72 150 L98 150 L106 132 L118 168 L126 150 L152 150',3.5,'#FFFFFF')
      + L('M156 168 C200 186 240 198 290 206',2.5,'#6C5CE7',.65)
      + C(290,206,4,'#6C5CE7')
      + fig(mDanger,216,58,.8)
      + spark(340,80,6,'#6C5CE7',.5)+spark(60,240,5,'#EC4899',.5)),
    'prog-newborn': svgA('Mother holding her newborn skin to skin',400,320,'#FAF8FD',
      archB(200,306,160,140,'rgba(108,92,231,.5)')
      + `<g class="floaty">${heart(66,84,8,'#EC4899',.8)}</g><g class="floaty" style="animation-delay:1.6s">${heart(336,96,9,'#6C5CE7',.8)}</g>`
      + spark(330,200,5,'#10B981',.5)
      + fig(mNewb,110,40,.9)),
    'prog-planning': svgA('Couple discussing family planning together',400,320,'#F3EEFA',
      `<g class="floaty">${heart(196,60,12,'#6C5CE7',.85)}</g><g class="floaty" style="animation-delay:1.5s">${heart(224,40,7,'#EC4899',.75)}</g>`
      + spark(60,80,6,'#F59E0B',.5)+spark(348,140,5,'#10B981',.5)
      + E(200,292,130,10,'rgba(30,27,75,.07)')
      + fig(mPlanP,28,40,.9)
      + fig(mPlanM,182,32,.9)
      + L('M162 203 C196 212 226 216 252 217',11,'#9C6B4F')
      + C(256,218,7.5,'#9C6B4F')),
    'prog-mind': svgA('Pregnant mother meditating beneath stars',400,320,'#F5ECE7',
      C(314,74,30,'#F59E0B')+C(326,66,26,'#F5ECE7')
      + spark(58,64,6,'#F59E0B',.6)+spark(352,150,5,'#F59E0B',.5)+C(340,58,3,'#F59E0B',.5)+C(70,130,2.5,'#F59E0B',.4)
      + sprig(34,312,.8,'#10B981')+sprig(366,312,.75,'#10B981')
      + fig(mMind,110,34,.95)),

    'vid-flow-t': vFlow.thumb,   'vid-flow-w': vFlow.wide,
    'vid-breath-t': vBrth.thumb, 'vid-breath-w': vBrth.wide,
    'vid-pelvic-t': vPelv.thumb, 'vid-pelvic-w': vPelv.wide,
    'vid-core-t': vCore.thumb,   'vid-core-w': vCore.wide,

    'testi-1': svgA('Portrait of an expectant mother',400,300,'#F3EEFA',
      C(200,118,108,'#EDE9FE',.5)+heart(306,172,9,'#6C5CE7',.7)+spark(88,96,5,'#6C5CE7',.5)
      + fig(mT1,50,46,1.5)),
    'testi-2': svgA('Portrait of a mother',400,300,'#EEF2EA',
      C(200,118,108,'rgba(16,185,129,.25)')+heart(300,170,9,'#10B981',.7)+spark(90,100,5,'#10B981',.5)
      + fig(mT2,50,46,1.5)),
    'testi-3': svgA('Portrait of an expectant mother',400,300,'#F3EEFA',
      C(200,118,108,'rgba(245,158,11,.25)')+heart(304,174,9,'#F59E0B',.7)+spark(88,98,5,'#F59E0B',.5)
      + fig(mT3,50,46,1.5)),

    'ask': svgA('Midwife and mother consulting with care',400,500,'#F3EEFA',
      archB(180,452,170,200,'rgba(16,185,129,.55)')
      + C(180,128,84,'#10B981',.2)
      + `<g class="floaty">${heart(243,246,11,'#6C5CE7',.85)}</g>`
      + spark(70,150,6,'#10B981',.5)+spark(340,120,6,'#6C5CE7',.5)+spark(64,300,5,'#F59E0B',.45)
      + sprig(40,470,1,'#10B981')
      + fig(mMid,60,108,1.2)
      + L('M398 266 C348 292 296 306 252 310',13,'#D89B78')
      + C(245,311,9,'#D89B78'))
  };
})();

/* Inject artwork */
$$('[data-art]').forEach(el => { const a = ART[el.dataset.art]; if (a) el.innerHTML = a; });
icons();

/* ================= Global Pregnancy Calculation Engine ================= */
function getLocalDateParts(d = new Date()) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

function calculatePregnancyDetails(dateStr, type = 'edd') {
  if (!dateStr) return null;
  const parts = dateStr.split('-');
  if (parts.length !== 3) return null;
  const y = parseInt(parts[0], 10), m = parseInt(parts[1], 10) - 1, d = parseInt(parts[2], 10);
  const targetDate = new Date(y, m, d);
  if (isNaN(targetDate.getTime())) return null;

  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

  let dueDate, lmpDate, daysPregnant, daysLeft;

  if (type === 'lmp') {
    lmpDate = new Date(targetDate);
    // Estimated Due Date = LMP + 280 days (40 weeks)
    dueDate = new Date(lmpDate.getTime() + 280 * 864e5);
    daysPregnant = Math.round((today.getTime() - lmpDate.getTime()) / 864e5);
    daysLeft = Math.round((dueDate.getTime() - today.getTime()) / 864e5);
  } else {
    // Estimated Due Date (EDD)
    dueDate = new Date(targetDate);
    lmpDate = new Date(dueDate.getTime() - 280 * 864e5);
    daysLeft = Math.round((dueDate.getTime() - today.getTime()) / 864e5);
    daysPregnant = 280 - daysLeft;
  }

  // Calculate current gestational week (1-40)
  let calculatedWeek = Math.floor(daysPregnant / 7) + 1;
  let clampedWeek = clamp(calculatedWeek, 1, 40);

  return {
    week: clampedWeek,
    rawWeek: calculatedWeek,
    daysPregnant: daysPregnant,
    daysLeft: daysLeft,
    dueDate: dueDate,
    lmpDate: lmpDate,
    isOverdue: daysLeft < 0,
    isDelivered: daysLeft <= -14
  };
}

const preg = { week: 24, lmp: null };
try {
  const savedLmp = localStorage.getItem('reprocare-lmp');
  if (savedLmp) {
    const res = calculatePregnancyDetails(savedLmp, 'lmp');
    if (res) {
      preg.lmp = savedLmp;
      preg.week = res.week;
    }
  }
} catch(e){}


/* ================= Nav ================= */
$('#menuBtn').addEventListener('click', () => document.body.classList.toggle('nav-open'));
$$('#navLinks a').forEach(a => a.addEventListener('click', () => document.body.classList.remove('nav-open')));

/* ================= Marquee ================= */
const mq = $('#mqTrack');
mq.innerHTML += mq.innerHTML;

/* ================= Scroll Reveal ================= */
const io = new IntersectionObserver(entries => {
  entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
}, { threshold: 0.12 });
$$('.reveal').forEach(el => io.observe(el));

/* ================= Book-a-checkup Scroll ================= */
function goBook(e){
  if (e) e.preventDefault();
  document.body.classList.remove('nav-open');
  const c = $('#apptCard');
  c.scrollIntoView({ behavior: 'smooth', block: 'center' });
  c.classList.remove('flash'); void c.offsetWidth; c.classList.add('flash');
}
$$('#navBook, .nav-book[href="#apptCard"], #heroBook').forEach(b => b.addEventListener('click', goBook));

/* ================= Appointment Booking ================= */
(function appt(){
  // Only runs for authenticated users — guest view doesn't have these elements
  const dateIn = document.getElementById('apDate');
  if (!dateIn) return; // guest view: bail out safely, no crash

  const form = $('#apForm'), done = $('#apDone'), err = $('#apErr'), errTxt = $('#apErrTxt');
  const times = $$('.ap-time');
  const pad = n => String(n).padStart(2, '0');
  const fmt = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  dateIn.min = fmt(new Date());
  dateIn.value = fmt(new Date(Date.now() + 864e5));
  let time = null;

  times.forEach(t => t.addEventListener('click', () => {
    time = t.dataset.t;
    times.forEach(x => { x.classList.toggle('on', x === t); x.setAttribute('aria-pressed', x === t); });
    if (err) err.hidden = true;
  }));

  const bookBtn = document.getElementById('apBook');
  if (bookBtn) bookBtn.addEventListener('click', () => {
    if (!dateIn.value || !time){
      if (err) err.hidden = false;
      if (errTxt) errTxt.textContent = !dateIn.value ? 'Please pick a preferred date.' : 'Please choose an appointment time.';
      return;
    }
    const d = new Date(dateIn.value + 'T12:00:00');
    const long = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    $('#apWhen').textContent = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    $('#apAt').textContent = `${time} · Barangay Health Station · SMS confirmation sent`;
    if (form) form.hidden = true;
    if (done) done.hidden = false;
    icons();
    const li = document.createElement('li');
    li.className = 'ap-new';
    li.innerHTML = `<span class="ap-d">${long} · <i>just booked</i></span><span class="ap-n">Prenatal check-up</span><span class="ap-l">Barangay Health Station</span>`;
    const apptList = document.getElementById('apptList');
    if (apptList) apptList.prepend(li);
    toast('Appointment scheduled — confirmed by SMS.', 'calendar-check');
  });

  const remindBtn = document.getElementById('apRemind');
  if (remindBtn) remindBtn.addEventListener('click', () => toast('SMS reminder set 3 days before visit.', 'bell'));

  const againBtn = document.getElementById('apAgain');
  if (againBtn) againBtn.addEventListener('click', () => {
    if (done) done.hidden = true;
    if (form) form.hidden = false;
    time = null;
    times.forEach(x => { x.classList.remove('on'); x.setAttribute('aria-pressed', 'false'); });
  });
})();

/* ================= Meet your baby: data ================= */
const TRI = [
  { max: 12, name: 'First trimester', c: '#10B981' },
  { max: 27, name: 'Second trimester', c: '#6C5CE7' },
  { max: 40, name: 'Third trimester', c: '#EC4899' }
];
const ANCHORS = [
  { w:4,  size:'a poppy seed',      len:0.1,  wt:0,
    n:"Neural tube, which forms your baby's brain and spinal cord, is developing rapidly.",
    t:"Start taking 400 µg of folic acid daily." },
  { w:6,  size:'a sesame seed',     len:0.6,  wt:0,
    n:"Tiny heartbeat flickers around 113 beats per minute as embryonic heart tubes fuse.",
    t:"Schedule your initial prenatal check-up at your health station." },
  { w:8,  size:'a raspberry',       len:1.6,  wt:1,
    n:"Limb buds are elongating and facial features begin to take distinct shape.",
    t:"Stay hydrated with frequent sips of water and plain crackers." },
  { w:10, size:'a kalamansi',       len:3.1,  wt:4,
    n:"All vital organ systems have formed blueprints; tiny fingernails are budding.",
    t:"Ensure you are eating balanced meals rich in iron and vitamin C." },
  { w:12, size:'a lanzones',        len:5.4,  wt:14,
    n:"First trimester milestone reached! Reflexes are developing and vocal cords form.",
    t:"Connect with your Barangay Health Worker for maternal tracking." },
  { w:14, size:'a cherry tomato',   len:8.7,  wt:43,
    n:"Baby can make gentle movements and thumb-sucking gestures inside the womb.",
    t:"Gentle walking and pelvic floor exercises are beneficial." },
  { w:16, size:'an avocado',        len:11.6, wt:100,
    n:"Quickening flutters may be felt by some mothers as muscles grow stronger.",
    t:"Ask about Tetanus Toxoid (TT) vaccination schedules at your visit." },
  { w:20, size:'a banana',          len:25.6, wt:300,
    n:"Halfway through pregnancy! Baby hears familiar voices and sounds outside the womb.",
    t:"Talk, read, or sing softly — baby recognizes your voice." },
  { w:22, size:'a big mango',       len:28,   wt:430,
    n:"Sensory development surges — taste buds, eyebrows, and sleep-wake cycles emerge.",
    t:"Practice sleeping on your left side with support pillows." },
  { w:24, size:'an ear of corn',    len:30,   wt:600,
    n:"Lungs are branching out airways; gestational diabetes screening window opens.",
    t:"Take the Oral Glucose Tolerance Test (OGTT) between weeks 24–28." },
  { w:26, size:'a large eggplant',  len:34,   wt:760,
    n:"Baby opens eyelashes and blinks in response to light filters through the womb.",
    t:"Enjoy a 10-minute prenatal breathing or stretching session." },
  { w:28, size:'a young coconut',   len:37.6, wt:1000,
    n:"Third trimester begins! Visits are scheduled every 2 weeks.",
    t:"Start daily kick counting at a consistent time each day." },
  { w:30, size:'a small cabbage',   len:39.9, wt:1300,
    n:"Rapid brain growth and subcutaneous fat storage prepare baby for warmth.",
    t:"Review newborn care and breastfeeding lessons." },
  { w:32, size:'a singkamas',       len:42.4, wt:1700,
    n:"Practicing breathing movements with amniotic fluid and exhibiting REM sleep.",
    t:"Prepare your hospital bag and review birth transport arrangements." },
  { w:34, size:'a cantaloupe',      len:45,   wt:2300,
    n:"Most babies settle into a head-down (cephalic) position ready for delivery.",
    t:"Review the 7 maternal danger signs with your partner." },
  { w:36, size:'a papaya',          len:47.4, wt:2600,
    n:"Entering weekly check-up phase as lungs and digestive tract mature fully.",
    t:"Learn the signs of true labor versus false labor." },
  { w:38, size:'a honeydew melon',  len:49,   wt:3000,
    n:"Baby is early-term; organ systems are primed for life outside the womb.",
    t:"Prioritize ample rest, proper hydration, and relaxed breathing." },
  { w:40, size:'a small watermelon',len:50.7, wt:3400,
    n:"Full term! Baby is fully ready to meet you and take their first breath.",
    t:"Stay calm, breathe deeply, and notify your health team upon labor signs." }
];
const FHR = [[6,113],[8,146],[10,168],[12,169],[16,154],[20,149],[24,146],[28,143],[32,141],[36,140],[40,139]];

const triOf = w => TRI.find(t => w <= t.max) || TRI[2];
const anchorFor = w => ANCHORS.filter(a => a.w <= w).pop() || ANCHORS[0];
function interp(w, key){
  for (let i = 0; i < ANCHORS.length - 1; i++){
    const a = ANCHORS[i], b = ANCHORS[i + 1];
    if (w >= a.w && w <= b.w) return a[key] + (b[key] - a[key]) * (w - a.w) / (b.w - a.w);
  }
  return w < ANCHORS[0].w ? ANCHORS[0][key] : ANCHORS[ANCHORS.length - 1][key];
}
function fhrOf(w){
  if (w < 6) return null;
  for (let i = 0; i < FHR.length - 1; i++){
    const [w1, v1] = FHR[i], [w2, v2] = FHR[i + 1];
    if (w >= w1 && w <= w2) return v1 + (v2 - v1) * (w - w1) / (w2 - w1);
  }
  return 139;
}
const fmtLen = cm => cm < 1 ? `${Math.round(cm * 10)} mm` : `${cm.toFixed(1)} cm`;
const fmtWt = g => g < 1 ? '—' : (g >= 1000 ? `${(g / 1000).toFixed(g >= 10000 ? 1 : 2)} kg` : `${Math.round(g)} g`);

/* ================= Meet your baby: scrubber ================= */
(function journey(){
  const handle = $('#scrubHandle'), track = $('#scrubTrack'), fill = $('#scrubFill'), wkTxt = $('#scrubWk');
  let curW = 1, shownW = 0, userTouched = false, sweepRAF = null;
  const posOf = w => (w - 1) / 39 * 100;

  function setWeek(w){
    curW = clamp(Math.round(w), 1, 40);
    const t = triOf(curW), a = anchorFor(curW);
    const len = interp(curW, 'len'), wt = interp(curW, 'wt'), bpm = fhrOf(curW);

    handle.style.left = posOf(curW) + '%';
    handle.style.borderColor = t.c;
    handle.setAttribute('aria-valuenow', curW);
    fill.style.width = posOf(curW) + '%';
    fill.style.background = t.c;
    wkTxt.textContent = curW;

    if (curW !== shownW){
      shownW = curW;
      $('#jWeekB').textContent = curW;
      const tri = $('#jTri');
      tri.textContent = t.name;
      tri.style.color = t.c; tri.style.borderColor = t.c + '66'; tri.style.background = t.c + '14';
      $('#jSize').textContent = `about the size of ${a.size}`;
      $('#jNote').textContent = a.n;
      $('#jTip').textContent = a.t;
      $('#jLen').textContent = fmtLen(len);
      $('#jWt').textContent = fmtWt(wt);
      const dur = bpm ? (60 / bpm).toFixed(3) + 's' : '1.15s';
      const stChip = $('#stChip'), bpmBox = $('#jBpmBox'), heroChip = $('#heroChip');
      [stChip, bpmBox, heroChip].forEach(el => el.style.setProperty('--pd', dur));
      const hasBpm = bpm != null;
      stChip.classList.toggle('still', !hasBpm);
      bpmBox.classList.toggle('still', !hasBpm);
      $('#stBpm').textContent = hasBpm ? Math.round(bpm) : '—';
      $('#jBpm').textContent = hasBpm ? Math.round(bpm) : '—';
    }
    const s = 0.14 + 0.86 * Math.pow(len / 50.7, 0.8);
    $('#babyG').style.transform = `scale(${s.toFixed(3)})`;
  }

  const dragTo = e => {
    const r = track.getBoundingClientRect();
    const p = clamp((e.clientX - r.left) / r.width, 0, 1);
    setWeek(1 + p * 39);
  };
  track.addEventListener('pointerdown', e => {
    userTouched = true;
    if (sweepRAF) cancelAnimationFrame(sweepRAF);
    track.setPointerCapture(e.pointerId);
    dragTo(e);
  });
  track.addEventListener('pointermove', e => { if (track.hasPointerCapture && track.hasPointerCapture(e.pointerId)) dragTo(e); });

  handle.addEventListener('keydown', e => {
    const step = { ArrowLeft: -1, ArrowDown: -1, ArrowRight: 1, ArrowUp: 1 }[e.key];
    if (step){ userTouched = true; setWeek(curW + step); e.preventDefault(); }
    else if (e.key === 'Home'){ setWeek(1); e.preventDefault(); }
    else if (e.key === 'End'){ setWeek(40); e.preventDefault(); }
  });

  function sweepTo(target){
    const t0 = performance.now(), dur = 2200;
    const ease = k => k < .5 ? 4 * k * k * k : 1 - Math.pow(-2 * k + 2, 3) / 2;
    const step = now => {
      if (userTouched) return;
      const k = Math.min(1, (now - t0) / dur);
      setWeek(1 + (target - 1) * ease(k));
      if (k < 1) sweepRAF = requestAnimationFrame(step);
    };
    sweepRAF = requestAnimationFrame(step);
  }

  window.__setPregWeek = w => { userTouched = true; if (sweepRAF) cancelAnimationFrame(sweepRAF); setWeek(w); };

  setWeek(preg.week);
  const jio = new IntersectionObserver(en => {
    if (en[0].isIntersecting){ jio.disconnect(); sweepTo(preg.week); }
  }, { threshold: 0.25 });
  jio.observe($('#jcard'));
})();

/* ================= Pregnancy Week Calculator (LMP → EDD) ================= */
// Runs AFTER journey() so window.__setPregWeek is defined
(function due(){
  var input   = document.getElementById('dueIn');
  var btn     = document.getElementById('dueBtn');
  var weekTxt = document.getElementById('dueChipTxt');
  var eddTxt  = document.getElementById('dueEddTxt');
  var daysEl  = document.getElementById('dueDaysLeft');
  var result  = document.getElementById('dueResult');

  if (!input) return;

  // Single toast slot — no stacking
  var lastToast = null;
  function showToast(msg) {
    if (lastToast) { lastToast.classList.remove('show'); try { lastToast.remove(); } catch(e){} }
    var t = document.createElement('div');
    t.className = 'toast';
    t.innerHTML = '<i data-lucide="heart"></i><span>' + msg + '</span>';
    var box = document.getElementById('toasts');
    if (box) box.appendChild(t);
    if (typeof lucide !== 'undefined') lucide.createIcons();
    requestAnimationFrame(function(){ t.classList.add('show'); });
    lastToast = t;
    setTimeout(function(){
      t.classList.remove('show');
      setTimeout(function(){ try { t.remove(); } catch(e){} lastToast = null; }, 450);
    }, 4000);
  }

  function formatDate(d) {
    // Format date as "Month D, YYYY" e.g. "March 15, 2027"
    var months = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
  }

  function calculate(withToast) {
    var v = input.value;
    if (!v) {
      if (result) result.style.display = 'none';
      return;
    }

    // Parse YYYY-MM-DD without timezone shift
    var p = v.split('-');
    if (p.length !== 3) return;
    var yr = parseInt(p[0], 10), mo = parseInt(p[1], 10) - 1, dy = parseInt(p[2], 10);
    if (isNaN(yr) || isNaN(mo + 1) || isNaN(dy)) return;

    var lmp   = new Date(yr, mo, dy);           // Last Menstrual Period date (local)
    var now   = new Date();
    var today = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // local midnight

    // EDD = LMP + 280 days (Naegele's rule)
    var edd = new Date(lmp.getTime() + 280 * 864e5);

    // Days pregnant = today − LMP
    var daysPregnant = Math.round((today - lmp) / 864e5);

    // Validate: LMP must be in the past (or today)
    if (daysPregnant < 0) {
      if (withToast) showToast('The last period date cannot be in the future.');
      if (result) result.style.display = 'none';
      return;
    }

    // Validate: not more than 10 months ago (280 + 30 days buffer)
    if (daysPregnant > 310) {
      if (withToast) showToast('That date seems too far in the past. Please check.');
      if (result) result.style.display = 'none';
      return;
    }

    // Calculate gestational week (1–40) and days left to EDD
    var week    = Math.min(40, Math.max(1, Math.floor(daysPregnant / 7) + 1));
    var daysLeft = Math.round((edd - today) / 864e5);
    var overdue  = daysLeft < 0;
    var tri      = triOf(week);

    // Save state
    preg.due  = v;
    preg.week = week;
    try {
      localStorage.setItem('reprocare-lmp', v);
    } catch(e){}

    // Move the week scrubber
    if (typeof window.__setPregWeek === 'function') {
      window.__setPregWeek(week);
    }

    // Update result panel
    if (result) result.style.display = 'block';
    if (typeof lucide !== 'undefined') lucide.createIcons();

    if (weekTxt) {
      weekTxt.textContent = tri.name + ' · Week ' + week + ' of 40';
    }
    if (eddTxt) {
      eddTxt.textContent = overdue
        ? formatDate(edd) + ' (delivered ' + Math.abs(daysLeft) + ' days ago)'
        : formatDate(edd);
    }
    if (daysEl) {
      daysEl.textContent = overdue
        ? 'Full term — baby has arrived!'
        : daysLeft + (daysLeft === 1 ? ' day' : ' days') + ' to due date';
    }

    // Update hero float chip
    var hcSub = document.getElementById('hcSub');
    var hcBpm = document.getElementById('hcBpm');
    var bpm   = fhrOf(week);
    if (hcSub) hcSub.textContent = 'bpm · week ' + week + ' · ' + (overdue ? 'delivered' : daysLeft + ' days to go');
    if (hcBpm) hcBpm.textContent = bpm ? Math.round(bpm) : '—';

    // Toast only on explicit button click
    if (withToast) {
      var msg = week <= 12
        ? 'Week ' + week + ' · ' + tri.name + ' — folic acid daily, vital organs forming!'
        : week <= 27
        ? 'Week ' + week + ' · ' + tri.name + ' — you may start feeling gentle movements!'
        : 'Week ' + week + ' · ' + tri.name + ' — counting kicks, preparing for delivery!';
      showToast(msg);
    }
  }

  // Button click → calculate + toast
  if (btn) btn.addEventListener('click', function() { calculate(true); });

  // Date picker change → silent recalculate
  input.addEventListener('change', function() { calculate(false); });

  // Restore saved LMP on page load
  try {
    var saved = localStorage.getItem('reprocare-lmp');
    if (saved) {
      input.value = saved;
      calculate(false);
      return;
    }
  } catch(e){}

  // Set sensible default: LMP ~24 weeks ago so slider lands on ~week 24
  var def = new Date(Date.now() - 24 * 7 * 864e5);
  var dy2 = def.getFullYear() + '-' + String(def.getMonth()+1).padStart(2,'0') + '-' + String(def.getDate()).padStart(2,'0');
  input.value = dy2;
  calculate(false);
})();

/* ================= Weight Chart ================= */
let wpts = [{w:8,g:-0.3},{w:12,g:0.5},{w:16,g:1.6},{w:20,g:3.0},{w:24,g:4.8},{w:28,g:6.6},{w:32,g:12.8},{w:36,g:13.9},{w:40,g:15.0}];
let chartDrawn = false;

function renderChart(){
  const svg = $('#wChart');
  const W = 640, H = 380, Lft = 54, R = 20, T = 28, B = 48;
  const X = w => Lft + (w - 8) / 32 * (W - Lft - R);
  const Y = g => T + (1 - (g + 2) / 20) * (H - T - B);
  const lo = w => w < 10 ? -0.5 : Math.min(11.5, -0.5 + (w - 10) * 0.38);
  const hi = w => w < 10 ?  0.5 : Math.min(16,    0.5 + (w - 10) * 0.52);

  let dUp = '', dDn = '';
  for (let w = 8; w <= 40; w++) dUp += (w === 8 ? 'M' : 'L') + X(w).toFixed(1) + ' ' + Y(hi(w)).toFixed(1);
  for (let w = 40; w >= 8; w--) dDn += 'L' + X(w).toFixed(1) + ' ' + Y(lo(w)).toFixed(1);

  const pts = [...wpts].sort((a, b) => a.w - b.w).map(p => {
    const st = p.g > hi(p.w) + 0.05 ? 'above' : (p.g < lo(p.w) - 0.05 ? 'below' : 'ok');
    return { ...p, x: X(p.w), y: Y(p.g), st };
  });

  let m = '';
  m += `<path id="wcBand" d="${dUp + dDn} Z" fill="rgba(16,185,129,.14)"/>`;
  m += `<path d="${dUp}" fill="none" stroke="rgba(16,185,129,.5)" stroke-width="1" stroke-dasharray="4 5"/>`;
  m += `<path d="M${dDn.slice(1)}" fill="none" stroke="rgba(16,185,129,.5)" stroke-width="1" stroke-dasharray="4 5"/>`;
  [0, 4, 8, 12, 16].forEach(g => {
    m += `<line x1="${Lft}" y1="${Y(g)}" x2="${W - R}" y2="${Y(g)}" stroke="rgba(108,92,231,.08)"/>`;
    m += `<text x="${Lft - 10}" y="${Y(g) + 4}" text-anchor="end" fill="rgba(30,27,75,.45)" style="font:600 11px 'Plus Jakarta Sans',sans-serif">${g}</text>`;
  });
  for (let w = 8; w <= 40; w += 4)
    m += `<text x="${X(w)}" y="${H - B + 20}" text-anchor="middle" fill="rgba(30,27,75,.45)" style="font:600 11px 'Plus Jakarta Sans',sans-serif">${w}</text>`;
  m += `<text x="${(Lft + W - R) / 2}" y="${H - 8}" text-anchor="middle" fill="rgba(30,27,75,.5)" style="font:800 10px 'Plus Jakarta Sans',sans-serif;letter-spacing:.14em">GESTATIONAL AGE · WEEKS</text>`;
  m += `<text transform="rotate(-90 14 ${(T + H - B) / 2})" x="14" y="${(T + H - B) / 2}" text-anchor="middle" fill="rgba(30,27,75,.5)" style="font:800 10px 'Plus Jakarta Sans',sans-serif;letter-spacing:.14em">WEIGHT GAIN · KG</text>`;

  m += `<path class="wcPatient" pathLength="1" fill="none" stroke="#6C5CE7" stroke-width="2.8" stroke-linejoin="round" stroke-linecap="round" d="${pts.map((p, i) => (i ? 'L' : 'M') + p.x.toFixed(1) + ' ' + p.y.toFixed(1)).join(' ')}"/>`;
  pts.forEach(p => {
    m += `<circle class="dot" cx="${p.x.toFixed(1)}" cy="${p.y.toFixed(1)}" r="${p.st === 'above' ? 6 : 5}" fill="${p.st === 'above' ? '#EC4899' : '#FFFFFF'}" stroke="#6C5CE7" stroke-width="2.5"/>`;
  });

  const fp = pts.find(p => p.st === 'above');
  if (fp){
    m += `<g class="flag-note">
      <line x1="${fp.x}" y1="${fp.y - 10}" x2="${fp.x}" y2="${fp.y - 26}" stroke="#EC4899" stroke-width="1.5"/>
      <circle cx="${fp.x}" cy="${fp.y - 35}" r="9" fill="#EC4899"/>
      <text x="${fp.x}" y="${fp.y - 31}" text-anchor="middle" fill="#FFFFFF" style="font:800 12px 'Plus Jakarta Sans',sans-serif">!</text>
      <text x="${fp.x}" y="${fp.y - 52}" text-anchor="middle" fill="#EC4899" style="font:italic 500 12.5px Fraunces,serif">Consult midwife next visit</text>
    </g>`;
  }
  m += `<line id="wcCross" y1="${T}" y2="${H - B}" stroke="rgba(108,92,231,.3)" stroke-width="1" stroke-dasharray="3 4" opacity="0"/>`;
  m += `<rect id="wcHit" x="${Lft}" y="${T}" width="${W - Lft - R}" height="${H - T - B}" fill="transparent" style="cursor:crosshair"/>`;
  svg.innerHTML = m;

  const tt = $('#wTt'), wrap = svg.parentElement;
  const cross = svg.querySelector('#wcCross');
  const dots = [...svg.querySelectorAll('.dot')];
  const stLabel = { ok: 'Within Expected Range', above: 'Above Expected Band', below: 'Below Expected Band' };
  svg.querySelector('#wcHit').addEventListener('pointermove', e => {
    const r = svg.getBoundingClientRect();
    const vx = (e.clientX - r.left) * W / r.width;
    let best = 0, bd = 1e9;
    pts.forEach((p, i) => { const d = Math.abs(p.x - vx); if (d < bd){ bd = d; best = i; } });
    const p = pts[best];
    cross.setAttribute('opacity', 1);
    cross.setAttribute('x1', p.x); cross.setAttribute('x2', p.x);
    dots.forEach((d, i) => d.setAttribute('r', i === best ? 7.5 : (pts[i].st === 'above' ? 6 : 5)));
    tt.innerHTML = `<b>Week ${p.w}</b><span class="tt-k">+${p.g.toFixed(1)} kg gain</span><span class="tt-s ${p.st}">${stLabel[p.st]}</span>`;
    const cw = wrap.clientWidth;
    tt.style.left = Math.max(76, Math.min(cw - 76, p.x / W * cw)) + 'px';
    tt.style.top = (p.y / H * wrap.clientHeight) + 'px';
    tt.classList.add('on');
  });
  svg.querySelector('#wcHit').addEventListener('pointerleave', () => {
    cross.setAttribute('opacity', 0); tt.classList.remove('on');
    dots.forEach((d, i) => d.setAttribute('r', pts[i].st === 'above' ? 6 : 5));
  });
}
renderChart();

const cio = new IntersectionObserver(en => {
  if (en[0].isIntersecting){ $('#wChart').classList.add('drawn'); chartDrawn = true; cio.disconnect(); }
}, { threshold: 0.3 });
cio.observe($('#chartCard'));

$('#logBtn').addEventListener('click', () => {
  const inp = $('#logGain'), msg = $('#logMsg');
  const v = parseFloat(inp.value);
  if (isNaN(v) || v < 0 || v > 40){
    msg.className = 'log-msg warn';
    msg.textContent = 'Please enter a valid weight gain in kg (e.g. 8.5).';
    return;
  }
  const w = clamp(preg.week, 8, 40);
  const existing = wpts.findIndex(p => p.w === w);
  if (existing >= 0) wpts[existing].g = v; else { wpts.push({ w, g: v }); wpts.sort((a, b) => a.w - b.w); }

  const lo = x => x < 10 ? -0.5 : Math.min(11.5, -0.5 + (x - 10) * 0.38);
  const hi = x => x < 10 ? 0.5 : Math.min(16, 0.5 + (x - 10) * 0.52);
  const st = v > hi(w) + 0.05 ? 'above' : (v < lo(w) - 0.05 ? 'below' : 'ok');

  renderChart();
  const svg = $('#wChart');
  if (chartDrawn){ svg.classList.remove('drawn'); void svg.getBoundingClientRect(); svg.classList.add('drawn'); }
  else svg.classList.add('drawn');

  if (st === 'ok'){
    msg.className = 'log-msg ok';
    msg.textContent = 'Right on track — healthy progression.';
    toast(`Week ${w} weight recorded. Right on track.`, 'check');
  } else if (st === 'above'){
    msg.className = 'log-msg warn';
    msg.textContent = 'Above expected gain range — mention this at your next visit with your midwife.';
    toast('Weight recorded. Flagged for review on next visit.', 'heart');
  } else {
    msg.className = 'log-msg warn';
    msg.textContent = 'Below expected gain range — mention this to your healthcare provider.';
    toast('Weight recorded. Mention this to your midwife.', 'heart');
  }
  inp.value = '';
});

/* ================= Reminders ================= */
$$('.switch').forEach(sw => sw.addEventListener('click', () => {
  const on = sw.getAttribute('aria-checked') !== 'true';
  sw.setAttribute('aria-checked', on);
  toast(`${sw.dataset.name} ${on ? 'enabled' : 'paused'}.`, on ? 'check' : 'bell');
}));

/* ================= Kick counter ================= */
(function kicks(){
  const btn = $('#kickBtn'), ring = $('#kickRing'), num = $('#kcNum'),
        timeEl = $('#kcTime'), stat = $('#kcStat'), acts = $('#kcActs');
  const CC = 552.9;
  let n = 0, t0 = null, done = false, int = null, elapsed = 0;
  const fmt = s => `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(Math.floor(s % 60)).padStart(2, '0')}`;
  function tick(){
    elapsed = (Date.now() - t0) / 1000;
    timeEl.innerHTML = fmt(elapsed) + '<span>elapsed</span>';
  }
  btn.addEventListener('click', () => {
    if (done) return;
    if (!t0){ t0 = Date.now(); int = setInterval(tick, 500); stat.textContent = 'Timer active — tap each time you feel a fetal movement.'; }
    n++;
    num.textContent = n;
    ring.style.strokeDashoffset = CC * (1 - n / 10);
    if (n === 9) stat.textContent = 'Almost there — one more kick...';
    if (n >= 10) finish();
  });
  function finish(){
    done = true;
    clearInterval(int);
    elapsed = (Date.now() - t0) / 1000;
    btn.classList.add('done');
    $('#kcFinal').textContent = fmt(elapsed);
    icons();
    stat.textContent = `10 movements recorded in ${fmt(elapsed)} — healthy fetal activity!`;
    acts.classList.add('on');
  }
  $('#kcSave').addEventListener('click', () => {
    toast('Daily kick count saved to maternal record.', 'check');
    stat.textContent = 'Saved to diary. Repeat tomorrow at around the same time.';
  });
  $('#kcReset').addEventListener('click', () => {
    done = false; n = 0; t0 = null; elapsed = 0;
    clearInterval(int);
    btn.classList.remove('done');
    num.textContent = '0';
    ring.style.strokeDashoffset = CC;
    timeEl.innerHTML = '00:00<span>elapsed</span>';
    stat.textContent = 'Tap the circle when you feel a movement — timer starts on first tap.';
    acts.classList.remove('on');
  });
})();

/* ================= Classes: Plan + Save ================= */
let plan = 0;
$$('.pcard').forEach(card => {
  const start = card.querySelector('.start'), save = card.querySelector('.save'), title = card.dataset.title;
  start.addEventListener('click', () => {
    const on = start.classList.toggle('added');
    if (on){ plan++; toast(`${title} added to your study plan.`, 'book-open'); }
    else { plan--; toast(`${title} removed from study plan.`, 'heart-off'); }
    $('#planCount').textContent = plan;
  });
  save.addEventListener('click', () => {
    const on = save.classList.toggle('saved');
    toast(on ? `${title} saved to bookmarks.` : `${title} removed from saved.`, 'heart');
  });
});

/* ================= Quiz ================= */
(function quiz(){
  const data = [
    { q: "Which supplement, taken daily before and during early pregnancy, prevents neural tube defects?",
      opts: ["Iron", "Folic acid", "Calcium", "Vitamin C"], a: 1,
      why: "400 µg of Folic Acid daily prevents over 70% of major neural tube and spinal defects." },
    { q: "You experience a severe headache and blurred vision at 32 weeks. What is the correct action?",
      opts: ["Rest at home and wait", "Go immediately to the nearest health station or hospital", "Drink extra juice", "Take paracetamol only"], a: 1,
      why: "Severe headache and visual disturbance are critical danger signs of pre-eclampsia requiring immediate medical attention." },
    { q: "According to DOH Unang Yakap protocol, when should the first breastfeeding happen?",
      opts: ["Within 6 hours", "After baby's first bath", "Within the first hour after birth", "When baby cries for milk"], a: 2,
      why: "Essential Newborn Care (Unang Yakap) mandates immediate skin-to-skin contact and initiation of breastfeeding within the first golden hour." }
  ];
  const body = $('#quizBody');
  let i = 0, score = 0, answered = false;

  function renderQ(){
    answered = false;
    body.innerHTML = `
      <div class="q-prog">
        <span class="q-dots">${data.map((_, k) => `<i class="${k === i ? 'cur' : ''}"></i>`).join('')}</span>
        <span class="q-count">Question ${i + 1} of ${data.length}</span>
      </div>
      <h4 class="q-q">${data[i].q}</h4>
      <div class="q-opts">${data[i].opts.map((o, k) =>
        `<button class="opt" data-k="${k}"><span class="opt-l">${'ABCD'[k]}</span><span>${o}</span></button>`).join('')}</div>
      <div class="q-fb" id="qFb" hidden></div>
      <div class="q-actions"><button class="btn btn-primary sm" id="qNext" hidden>${i === data.length - 1 ? 'See Results' : 'Next Question'} <i data-lucide="arrow-right"></i></button></div>`;
    icons();
    body.querySelectorAll('.opt').forEach(b => b.addEventListener('click', () => choose(+b.dataset.k)));
    body.querySelector('#qNext').addEventListener('click', () => { i++; i < data.length ? renderQ() : renderResult(); });
  }
  function choose(k){
    if (answered) return;
    answered = true;
    const q = data[i], ok = k === q.a;
    if (ok) score++;
    body.querySelectorAll('.opt').forEach((b, idx) => {
      b.disabled = true;
      if (idx === q.a) b.classList.add('correct');
      else if (idx === k) b.classList.add('wrong');
    });
    const fb = body.querySelector('#qFb');
    fb.hidden = false; fb.classList.add(ok ? 'ok' : 'no');
    fb.innerHTML = `<i data-lucide="${ok ? 'check' : 'alert-triangle'}"></i><p><b>${ok ? 'Correct!' : 'Not quite.'}</b> ${q.why}</p>`;
    body.querySelectorAll('.q-dots i')[i].className = ok ? 'ok' : 'no';
    icons();
    body.querySelector('#qNext').hidden = false;
  }
  function renderResult(){
    const msg = score === 3 ? "Excellent! You have a great grasp of maternal health essentials."
      : score === 2 ? "Great job! A quick review of our modules will make you fully prepared."
      : "Good effort! Explore our free classes to learn more about a safe pregnancy.";
    body.innerHTML = `
      <div class="q-result">
        <span class="q-score">${score}<span>/${data.length}</span></span>
        <p class="q-msg">${msg}</p>
        <p class="q-note">Explore all 6 free ReproCare maternal learning modules anytime.</p>
        <button class="btn btn-ghost sm" id="qRestart">Retake Quiz</button>
      </div>`;
    body.querySelector('#qRestart').addEventListener('click', () => { i = 0; score = 0; renderQ(); });
  }
  renderQ();
})();

/* ================= Wellness ================= */
(function wellness(){
  const SESSIONS = [
    { t: 'First-Trimester Gentle Flow', min: '18 min', lvl: 'Gentle', tri: 'Trimester 1', artW: ART['vid-flow-w'],
      d: 'Supported, gentle movement designed for early weeks — soothing morning fatigue, opening hips, and calming breathing exercises.',
      pts: ['Mat and sturdy chair for support', 'Alleviates early pregnancy nausea and stiffness', 'Ends with 3 minutes of supported relaxation'] },
    { t: 'Breathing for Labor & Birth', min: '12 min', lvl: 'All levels', tri: 'All trimesters', artW: ART['vid-breath-w'],
      d: 'Evidence-based breathing techniques to manage contraction surges and maintain steady oxygenation during labor.',
      pts: ['Quiet and comfortable seating', 'Trains steady exhalation patterns', 'Partners can practice together'] },
    { t: 'Pelvic Floor Foundations', min: '10 min', lvl: 'Gentle', tri: 'All trimesters', artW: ART['vid-pelvic-w'],
      d: 'Seated pelvic floor awareness, gentle engagement, and essential relaxation to prevent strain and assist delivery.',
      pts: ['Comfortable seated position', 'Enhances bladder control and birth recovery', 'Short and easy to perform daily'] },
    { t: 'Postpartum Core Restore', min: '15 min', lvl: 'Gentle', tri: 'Postpartum', artW: ART['vid-core-w'],
      d: 'Gentle breath-synchronized core and pelvic realignment for postpartum recovery after medical clearance.',
      pts: ['Requires clearance from your 6-week postnatal check-up', 'Safe alongside breastfeeding', 'Gentle realignment with no strain'] }
  ];
  const cards = $$('.vcard'), modal = $('#vModal'), doneBtn = $('#vmDone');
  const CIRC = 188.5, completed = new Set();
  let cur = 0, lastFocus = null;

  function updateRing(){
    const n = completed.size;
    $('#ringFg').style.strokeDashoffset = CIRC * (1 - n / 4);
    $('#ringTxt').textContent = `${n}/4`;
    $('#ringN').textContent = n;
  }
  function openModal(i){
    cur = i;
    const s = SESSIONS[i];
    $('#vmArt').innerHTML = s.artW;
    $('#vmDur').textContent = s.min;
    $('#vmLvl').textContent = s.lvl;
    $('#vmTri').textContent = s.tri;
    $('#vmTitle').textContent = s.t;
    $('#vmDesc').textContent = s.d;
    $('#vmList').innerHTML = s.pts.map(p => `<li><i data-lucide="heart"></i><span>${p}</span></li>`).join('');
    doneBtn.classList.toggle('is-done', completed.has(i));
    icons();
    lastFocus = document.activeElement;
    modal.hidden = false;
    requestAnimationFrame(() => requestAnimationFrame(() => modal.classList.add('open')));
    document.body.classList.add('modal-open');
    modal.querySelector('.modal-x').focus();
  }
  function closeModal(){
    modal.classList.remove('open');
    document.body.classList.remove('modal-open');
    setTimeout(() => { modal.hidden = true; if (lastFocus) lastFocus.focus(); }, 280);
  }
  cards.forEach((c, i) => {
    c.addEventListener('click', () => openModal(i));
    c.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' '){ e.preventDefault(); openModal(i); } });
  });
  $$('[data-close]').forEach(el => el.addEventListener('click', closeModal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
  doneBtn.addEventListener('click', () => {
    if (completed.has(cur)) return;
    completed.add(cur);
    cards[cur].classList.add('done');
    doneBtn.classList.add('is-done');
    updateRing();
    toast('Movement session completed!', 'heart');
    setTimeout(closeModal, 500);
  });
})();

/* ================= Care Circle Pulse ================= */
(function chain(){
  const chainEl = $('#chain'), line = $('#chainLine'), dot = $('#chainDot');
  const nodes = $$('.chain-node');
  let pts = [], active = true;

  function measure(){
    const cr = chainEl.getBoundingClientRect();
    pts = nodes.map(n => {
      const ir = n.querySelector('.chain-ico').getBoundingClientRect();
      return { x: ir.left - cr.left + ir.width / 2, y: ir.top - cr.top + ir.height / 2 };
    });
    const a = pts[0], b = pts[pts.length - 1];
    if (innerWidth <= 780){
      line.style.left = (a.x - .75) + 'px'; line.style.top = a.y + 'px';
      line.style.width = '1.5px'; line.style.height = (b.y - a.y) + 'px';
    } else {
      line.style.left = a.x + 'px'; line.style.top = (a.y - .75) + 'px';
      line.style.width = (b.x - a.x) + 'px'; line.style.height = '1.5px';
    }
  }
  measure();
  addEventListener('resize', measure);
  addEventListener('load', measure);

  const vio = new IntersectionObserver(en => active = en[0].isIntersecting, { threshold: 0 });
  vio.observe(chainEl);

  const DUR = 5200;
  function frame(now){
    if (active && pts.length){
      const t = (now % DUR) / DUR;
      const a = pts[0], b = pts[pts.length - 1];
      dot.style.transform = `translate(${a.x + (b.x - a.x) * t}px, ${a.y + (b.y - a.y) * t}px)`;
      nodes.forEach((n, idx) => {
        const p = idx / (nodes.length - 1);
        n.classList.toggle('lit', t >= p - 0.001 && t < p + 0.12);
      });
    }
    requestAnimationFrame(frame);
  }
  requestAnimationFrame(frame);
})();

/* ================= FAQ Accordions ================= */
$$('[data-acc]').forEach(acc => {
  acc.querySelectorAll('.acc-row > button').forEach(head => head.addEventListener('click', () => {
    const row = head.parentElement, wasOpen = row.classList.contains('open');
    acc.querySelectorAll('.acc-row.open').forEach(r => {
      r.classList.remove('open');
      r.querySelector('button').setAttribute('aria-expanded', 'false');
    });
    if (!wasOpen){ row.classList.add('open'); head.setAttribute('aria-expanded', 'true'); }
  }));
});

/* ================= Ask a Midwife Form ================= */
(function ask(){
  const form = $('#askForm'), ok = $('#askOk');
  const name = $('#afName'), contact = $('#afContact'), msg = $('#afMsg');
  const isMail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  const isPhone = v => /^[\d+\-\s()]{7,}$/.test(v);

  form.addEventListener('submit', e => {
    e.preventDefault();
    let valid = true;
    const check = (cond, errEl) => { errEl.classList.toggle('on', !cond); if (!cond) valid = false; };
    check(name.value.trim().length >= 2, $('#errName'));
    check(isMail(contact.value.trim()) || isPhone(contact.value.trim()), $('#errContact'));
    check(msg.value.trim().length >= 10, $('#errMsg'));
    if (!valid) return;
    form.hidden = true; ok.hidden = false;
    icons();
    toast('Inquiry submitted — a midwife will respond shortly.', 'send');
  });
  [name, contact, msg].forEach((el, i) =>
    el.addEventListener('input', () => $$('.ferr')[i].classList.remove('on')));
})();

/* ================= Footer Year ================= */
$('#yr').textContent = new Date().getFullYear();
</script>
</body>
</html>
