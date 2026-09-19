* { margin: 0; padding: 0; box-sizing: border-box; }
:root {
    --primary: #5B2A86;
    --primary-dark: #431D65;
    --primary-light: #F3ECF8;
    --text: #21182A;
    --muted: #77717D;
    --background: #FAF9FB;
    --white: #FFFFFF;
    --border: #E9E4EC;
    --shadow: 0 10px 30px rgba(40, 20, 55, 0.07);
}
body { font-family: 'Inter', sans-serif; background: var(--background); color: var(--text); line-height: 1.5; }
a { text-decoration: none; color: inherit; }
button, input, select, textarea { font-family: inherit; }

nav {
    height: 76px; padding: 0 7%; background: var(--white); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000;
}
.logo { color: var(--primary) !important; font-size: 25px; font-weight: 700; letter-spacing: 1.5px; }
.nav-links { display: flex; align-items: center; gap: 32px; list-style: none; }
.nav-links a { color: #625B67; font-size: 13px; font-weight: 500; transition: 0.2s; }
.nav-links a:hover { color: var(--primary); }
.nav-links .active { color: var(--primary); font-weight: 600; }
.nav-actions { display: flex; align-items: center; gap: 18px; }
.login { padding: 9px 17px; border: 1px solid var(--border); border-radius: 6px; color: var(--text); font-size: 12px; font-weight: 600; transition: 0.2s; }
.login:hover { border-color: var(--primary); color: var(--primary); }
.cart { width: 38px; height: 38px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 17px; transition: 0.2s; position: relative; }
.cart:hover { background: var(--primary); color: white; }
.cart span { position: absolute; top: -4px; right: -4px; background: #E85D5D; color: white; font-size: 10px; border-radius: 999px; padding: 1px 5px; font-weight: 700; }

.page-header { padding: 45px 7% 25px; }
.breadcrumb { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
.breadcrumb span { color: var(--primary); font-weight: 600; }
.page-header h1 { font-size: 34px; margin-bottom: 8px; }
.page-header p { color: var(--muted); font-size: 13px; }

.status-banner { margin: 0 7% 20px; background: var(--primary-light); color: var(--primary); padding: 10px 16px; border-radius: 8px; font-size: 13px; }
.error-banner { margin: 0 7% 20px; background: #FDEAEA; color: #C23B3B; padding: 10px 16px; border-radius: 8px; font-size: 13px; }

.badge { display:inline-block; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.badge-toship { background:#FEF3C7; color:#92400E; }
.badge-transit { background:#DBEAFE; color:#1D4ED8; }
.badge-outfordelivery { background:#EDE9FE; color:#6D28D9; }
.badge-delivered { background:#DCFCE7; color:#15803D; }
.badge-cancelled { background:#FEE2E2; color:#B91C1C; }

.btn-primary-solid { background: var(--primary); color: white; border: none; padding: 12px 22px; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; display:inline-block; text-align:center; }
.btn-primary-solid:hover { background: var(--primary-dark); }
.btn-outline { background: white; color: var(--primary); border: 1px solid var(--border); padding: 12px 22px; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; transition:0.2s; display:inline-block; text-align:center; }
.btn-outline:hover { border-color: var(--primary); }