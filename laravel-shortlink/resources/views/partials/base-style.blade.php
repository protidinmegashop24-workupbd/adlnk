body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:0;color:#222}
.tabs{display:flex;gap:6px;margin-top:20px;background:#eef1f5;border-radius:10px;padding:5px}
.tab{flex:1;background:transparent;color:#444;border:0;padding:10px;border-radius:8px;font-size:14px;cursor:pointer;margin-top:0}
.tab.active{background:#fff;color:#0d6efd;box-shadow:0 1px 3px rgba(0,0,0,.12);font-weight:bold}
.card{background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
label{display:block;font-size:13px;color:#555;margin:12px 0 4px}
input[type=text],input[type=url],input[type=email],input[type=password],input[type=datetime-local]{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px}
textarea{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:14px;font-family:inherit;resize:vertical}
button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px;width:100%}
button:disabled{background:#9db8e8;cursor:not-allowed}
.error{color:#c0392b;margin-top:10px;font-size:14px}
.status{color:#1a7f37;background:#e8f8ee;border:1px solid #c3ecd2;padding:10px 14px;border-radius:6px;font-size:14px;margin-top:16px}
.result{display:none;margin-top:20px;text-align:center;border-top:1px solid #eee;padding-top:20px}
.result input{text-align:center;font-weight:bold;margin-bottom:12px}
.result img{border:1px solid #eee;border-radius:8px;margin:8px 0}
.copybtn{background:#28a745}
.muted{color:#888;font-size:13px;text-align:center;margin-top:24px}
.hint{color:#888;font-size:12px;margin-top:6px}
.auth-switch{text-align:center;font-size:13px;color:#555;margin-top:16px}
table{width:100%;border-collapse:collapse;margin-top:16px;font-size:13px}
th,td{text-align:left;padding:8px;border-bottom:1px solid #eee;vertical-align:middle}
td.url-col{max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.del-btn{background:#c0392b;width:auto;padding:6px 12px;margin:0;font-size:12px}
.pagination{display:flex;gap:8px;justify-content:center;margin-top:16px;font-size:13px}
.pagination a,.pagination span{padding:6px 10px;border:1px solid #ddd;border-radius:4px;color:#0d6efd;text-decoration:none}
.bulk-results{display:none;margin-top:20px;border-top:1px solid #eee;padding-top:16px}
.bulk-row{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f1f1;font-size:13px}
.bulk-row:last-child{border-bottom:0}
.bulk-row .orig{color:#888;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:45%}
.bulk-row .short-link{font-weight:bold}
.bulk-row .bulk-error{color:#c0392b}
