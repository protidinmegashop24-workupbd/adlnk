body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:0;color:#222;font-size:16px}
.tabs{display:flex;gap:6px;margin-top:20px;background:#eef1f5;border-radius:10px;padding:5px}
.tab{flex:1;background:transparent;color:#444;border:0;padding:10px;border-radius:8px;font-size:15px;cursor:pointer;margin-top:0}
.tab.active{background:#fff;color:#0d6efd;box-shadow:0 1px 3px rgba(0,0,0,.12);font-weight:bold}
.card{background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
label{display:block;font-size:14px;color:#555;margin:12px 0 4px}
input[type=text],input[type=url],input[type=email],input[type=password],input[type=datetime-local],select{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:16px;background:#fff}
textarea{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px;font-family:inherit;resize:vertical}
button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:16px;cursor:pointer;margin-top:16px;width:100%}
button:disabled{background:#9db8e8;cursor:not-allowed}
.error{color:#c0392b;margin-top:10px;font-size:15px}
.status{color:#1a7f37;background:#e8f8ee;border:1px solid #c3ecd2;padding:10px 14px;border-radius:6px;font-size:15px;margin-top:16px}
.result{display:none;margin-top:20px;text-align:center;border-top:1px solid #eee;padding-top:20px}
.result input{text-align:center;font-weight:bold;margin-bottom:12px}
.result img{border:1px solid #eee;border-radius:8px;margin:8px 0}
.copybtn{background:#28a745}
.muted{color:#888;font-size:14px;text-align:center;margin-top:24px}
.hint{color:#888;font-size:13px;margin-top:6px}
.auth-switch{text-align:center;font-size:14px;color:#555;margin-top:16px}
table{width:100%;border-collapse:collapse;margin-top:16px;font-size:14px}
th,td{text-align:left;padding:8px;border-bottom:1px solid #eee;vertical-align:middle}
td.url-col{max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.del-btn{background:#c0392b;width:auto;padding:6px 12px;margin:0;font-size:13px}
.pagination{display:flex;gap:8px;justify-content:center;margin-top:16px;font-size:14px}
.pagination a,.pagination span{padding:6px 10px;border:1px solid #ddd;border-radius:4px;color:#0d6efd;text-decoration:none}
.bulk-results{display:none;margin-top:20px;border-top:1px solid #eee;padding-top:16px}
.bulk-row{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f1f1;font-size:14px}
.bulk-row:last-child{border-bottom:0}
.bulk-row .orig{color:#888;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:45%}
.bulk-row .short-link{font-weight:bold}
.bulk-row .bulk-error{color:#c0392b}
details.faq-item{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:14px 18px;margin-bottom:10px;max-width:700px;margin-left:auto;margin-right:auto}
details.faq-item summary{cursor:pointer;font-weight:bold;font-size:15px}
details.faq-item p{margin:10px 0 0;color:#555;font-size:15px}
.breadcrumb{font-size:13px;color:#888;margin-bottom:14px}
.breadcrumb a{color:#0d6efd;text-decoration:none}
.breadcrumb a:hover{text-decoration:underline}
.tool-section{max-width:760px;margin:0 auto}
.tool-section h2{font-size:1.25rem;margin-top:40px}
.tool-section h3{font-size:1.05rem;margin-top:20px}
.tool-section p,.tool-section li{font-size:15px;color:#444;line-height:1.6}
.tool-result{margin-top:20px}
.result-item{border:1px solid #e5e7eb;border-radius:8px;padding:14px 16px;margin-top:10px;background:#fff}
.result-item .r-head{display:flex;align-items:center;gap:10px;font-weight:bold;font-size:14px}
.result-badge{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;color:#fff;font-size:13px;font-weight:bold;flex-shrink:0}
.result-badge.pass{background:#1a7f37}
.result-badge.warning{background:#b8860b}
.result-badge.missing,.result-badge.problem{background:#c0392b}
.result-item .r-finding{font-size:14px;color:#333;margin-top:8px}
.result-item .r-explain{font-size:13px;color:#666;margin-top:4px}
.result-item .r-action{font-size:13px;color:#0d6efd;margin-top:6px;font-weight:bold}
.related-tools{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.related-tools a{background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:8px 16px;font-size:13px;text-decoration:none;color:#222}
.related-tools a:hover{border-color:#0d6efd;color:#0d6efd}
.notice-box{background:#f4f6f8;border:1px solid #e0e0e0;border-radius:8px;padding:12px 16px;font-size:13px;color:#555;margin-top:16px}
.char-count{font-size:12px;color:#888;text-align:right;margin-top:4px}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px;margin-top:16px}
.stat-tile{background:#fff;border:1px solid #eee;border-radius:8px;padding:12px;text-align:center}
.stat-tile .snum{font-size:1.3rem;font-weight:bold;color:#0d6efd}
.stat-tile .slabel{font-size:11px;color:#888;text-transform:uppercase;margin-top:2px}
.final-cta{text-align:center;margin-top:48px;padding:36px 20px;background:#0d6efd;border-radius:12px;color:#fff}
.final-cta h2{color:#fff;margin-top:0}
.final-cta a.cta-btn{display:inline-block;background:#fff;color:#0d6efd;padding:12px 28px;border-radius:6px;font-weight:bold;text-decoration:none;margin-top:8px}
.social-preview-card{border:1px solid #ddd;border-radius:8px;overflow:hidden;max-width:420px;margin:16px auto 0;background:#fff;text-align:left}
.social-preview-card .sp-image{width:100%;aspect-ratio:1.91/1;background:#eee;object-fit:cover;display:block}
.social-preview-card .sp-body{padding:10px 14px}
.social-preview-card .sp-domain{font-size:11px;color:#888;text-transform:uppercase}
.social-preview-card .sp-title{font-size:14px;font-weight:bold;margin-top:2px;color:#222}
.social-preview-card .sp-desc{font-size:12px;color:#666;margin-top:2px}
.serp-preview-card{border:1px solid #eee;border-radius:8px;padding:14px 16px;max-width:600px;margin:16px auto 0;font-family:Arial,sans-serif}
.serp-preview-card .sp-url{font-size:13px;color:#202124}
.serp-preview-card .sp-title{font-size:18px;color:#1a0dab;margin-top:2px;line-height:1.3}
.serp-preview-card .sp-desc{font-size:13px;color:#4d5156;margin-top:4px;line-height:1.4}
.serp-tabs{display:flex;gap:6px;max-width:600px;margin:16px auto 0}
.serp-tabs button{width:auto;flex:1;margin:0;padding:8px;background:#eef1f5;color:#444;font-size:13px}
.serp-tabs button.active{background:#0d6efd;color:#fff}
