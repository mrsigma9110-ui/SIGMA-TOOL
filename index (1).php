<?php
session_start();
$file = 'tools.json';
$correct_password = "ursigma91"; 

if (!file_exists($file)) { file_put_contents($file, json_encode([])); }
$tools = json_decode(file_get_contents($file), true);

$is_admin = isset($_GET['admin']);
$toast = "";

if ($is_admin) {
    if (isset($_POST['login'])) {
        if ($_POST['pass'] === $correct_password) { $_SESSION['auth'] = true; $toast = "Access Granted!"; }
        else { $toast = "Invalid Password!"; }
    }
    if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

    if (isset($_SESSION['auth'])) {
        if (isset($_POST['save_tool'])) {
            $new_tool = ['id' => $_POST['id'] ?: time(), 'name' => $_POST['name'], 'link' => $_POST['link'], 'icon' => $_POST['icon'], 'type' => $_POST['type']];
            if ($_POST['id']) {
                foreach ($tools as $key => $t) { if ($t['id'] == $_POST['id']) $tools[$key] = $new_tool; }
                $toast = "Tool Updated!";
            } else { $tools[] = $new_tool; $toast = "Tool Added!"; }
            file_put_contents($file, json_encode(array_values($tools)));
        }
        if (isset($_GET['del'])) {
            $tools = array_filter($tools, function($t) { return $t['id'] != $_GET['del']; });
            file_put_contents($file, json_encode(array_values($tools)));
            $toast = "Tool Removed!";
        }
    }
}
$edit_data = null;
if (isset($_GET['edit']) && isset($_SESSION['auth'])) {
    foreach ($tools as $t) { if ($t['id'] == $_GET['edit']) $edit_data = $t; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>↺𝙐𝙍•𝙎𝙄𝙂𝙈𝘼⤸𓆪ꪾ🎭</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --bg: #e0e0e0; --sh-dark: #bebebe; --sh-light: #ffffff; --accent: #00d2ff; }
        body { background: var(--bg); font-family: 'Orbitron', sans-serif; }
        
        .tool-card { background: var(--bg); border-radius: 25px; box-shadow: 10px 10px 20px var(--sh-dark), -10px -10px 20px var(--sh-light); padding: 25px; display: flex; flex-direction: column; align-items: center; text-decoration: none !important; transition: 0.3s; height: 190px; border: 1px solid rgba(255,255,255,0.3); margin-bottom: 10px; }
        .tool-card:active { box-shadow: inset 5px 5px 10px var(--sh-dark); transform: scale(0.96); }
        
        .icon-box { width: 85px; height: 85px; background: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; padding: 12px; box-shadow: inset 4px 4px 8px var(--sh-dark); margin-bottom: 15px; overflow: hidden; }
        .icon-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        
        .tool-name { font-size: 0.75rem; font-weight: 800; color: #222; text-transform: uppercase; text-align: center; }

        .admin-box { max-width: 480px; margin: 50px auto; padding: 35px; border-radius: 40px; background: var(--bg); box-shadow: 25px 25px 50px var(--sh-dark), -25px -25px 50px var(--sh-light); border: 1px solid rgba(255,255,255,0.5); }
        .inp { width: 100%; padding: 16px; margin: 12px 0; border: none; border-radius: 18px; background: var(--bg); box-shadow: inset 8px 8px 16px var(--sh-dark), inset -8px -8px 16px var(--sh-light); outline: none; font-family: 'Orbitron'; color: #444; }
        .btn-3d { width: 100%; padding: 16px; border-radius: 18px; border: none; background: var(--bg); box-shadow: 8px 8px 16px var(--sh-dark), -8px -8px 16px var(--sh-light); font-weight: 900; cursor: pointer; color: #333; transition: 0.2s; }
        .btn-3d:active { box-shadow: inset 5px 5px 10px var(--sh-dark); transform: scale(0.97); }
        .btn-blue { background: var(--accent); color: white; }

        #toast { visibility: hidden; min-width: 280px; background: #222; color: #fff; text-align: center; border-radius: 50px; padding: 16px; position: fixed; z-index: 999; left: 50%; bottom: 40px; transform: translateX(-50%); font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        #toast.show { visibility: visible; animation: slideup 0.5s, slidedown 0.5s 2.5s; }
        @keyframes slideup { from {bottom: 0; opacity: 0;} to {bottom: 40px; opacity: 1;} }
        @keyframes slidedown { from {bottom: 40px; opacity: 1;} to {bottom: 0; opacity: 0;} }

        .cat-btn { width: 135px; padding: 14px; background: var(--bg); border-radius: 15px; box-shadow: 6px 6px 12px var(--sh-dark), -6px -6px 12px var(--sh-light); border: none; font-size: 0.75rem; font-weight: 900; margin: 0 8px; }
        .cat-btn.active { box-shadow: inset 6px 6px 12px var(--sh-dark); color: var(--accent); }
    </style>
</head>
<body>

<div id="toast"><?= $toast ?></div>

<?php if (!$is_admin): ?>
    <div class="text-center mt-5 mb-4">
        <h1 style="font-weight:900; letter-spacing:3px; color:#222;">SIGMA TOOLS</h1>
        <p style="font-size:0.7rem; font-weight:bold; color:var(--accent);">PREMIUM DIGITAL TOOLKIT</p>
    </div>

    <div class="container" style="max-width:550px;">
        <input type="text" id="toolSearch" class="inp" placeholder="SEARCH TOOLS..." onkeyup="filter()" style="padding-left:20px;">
        
        <div class="d-flex justify-content-center my-4">
            <button class="cat-btn active" id="t-btn" onclick="tab('tool')"><i class="bi bi-gear-fill me-1"></i> TOOLS</button>
            <button class="cat-btn" id="s-btn" onclick="tab('source')"><i class="bi bi-code-slash me-1"></i> SOURCE</button>
        </div>

        <div id="grid" class="row g-4">
            <?php foreach($tools as $t): ?>
            <div class="col-6 item" data-type="<?= $t['type'] ?>">
                <a href="<?= $t['link'] ?>" target="_blank" class="tool-card">
                    <div class="icon-box"><img src="<?= $t['icon'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1243/1243420.png'"></div>
                    <span class="tool-name"><?= $t['name'] ?></span>
                    <span style="font-size:0.45rem; color:#888; margin-top:12px; font-weight:bold;">LEGENDARY VIP</span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="text-center mt-5 py-4">
        <div style="font-size: 0.85rem; font-weight:900; letter-spacing:4px; color:#444;">SIGMA HACKER</div>
        <small style="color:#999; font-size:9px;">© POWER BY WASEEM</small>
    </footer>

<?php else: ?>
    <div class="admin-box">
        <?php if (!isset($_SESSION['auth'])): ?>
            <h3 class="text-center mb-4" style="font-weight:900;">ADMIN LOGIN</h3>
            <form method="POST">
                <input type="password" name="pass" class="inp" placeholder="Enter Password" required>
                <button name="login" class="btn-3d btn-blue mt-3">LOGIN</button>
            </form>
            <a href="index.php" class="btn-3d mt-3 text-center d-block text-decoration-none">BACK</a>
        <?php else: ?>
            <h4 class="text-center mb-4" style="font-weight:900;"><?= $edit_data ? 'EDIT TOOL' : 'ADD NEW TOOL' ?></h4>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
                <input type="text" name="name" class="inp" placeholder="Tool Name" value="<?= $edit_data['name'] ?? '' ?>" required>
                <input type="text" name="link" class="inp" placeholder="Link (e.g. https://google.com)" value="<?= $edit_data['link'] ?? '' ?>" required>
                <input type="text" name="icon" class="inp" placeholder="Icon URL" value="<?= $edit_data['icon'] ?? '' ?>" required>
                <select name="type" class="inp">
                    <option value="tool" <?= (isset($edit_data) && $edit_data['type']=='tool')?'selected':'' ?>>TOOL</option>
                    <option value="source" <?= (isset($edit_data) && $edit_data['type']=='source')?'selected':'' ?>>SOURCE</option>
                </select>
                <button name="save_tool" class="btn-3d btn-blue mt-2"><?= $edit_data ? 'UPDATE' : 'UPLOAD' ?></button>
            </form>
            
            <div style="margin-top:30px; max-height: 250px; overflow-y:auto;">
                <?php foreach($tools as $t): ?>
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-4 shadow-sm">
                    <span style="font-size:11px; font-weight:bold;"><?= $t['name'] ?></span>
                    <div>
                        <a href="?admin&edit=<?= $t['id'] ?>" class="text-primary me-3"><i class="bi bi-pencil-fill"></i></a>
                        <a href="?admin&del=<?= $t['id'] ?>" class="text-danger"><i class="bi bi-trash3-fill"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <a href="index.php" class="btn-3d mt-4 text-center d-block text-decoration-none" style="background:#444; color:white;">BACK TO SITE</a>
            <center><a href="?admin&logout" class="text-danger small mt-4 d-block font-weight-bold">Logout</a></center>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    window.onload = function() {
        let msg = "<?= $toast ?>";
        if(msg) {
            let x = document.getElementById("toast");
            x.className = "show";
            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
        }
    }
    let view = 'tool';
    function tab(t) {
        view = t;
        document.getElementById('t-btn').classList.toggle('active', t==='tool');
        document.getElementById('s-btn').classList.toggle('active', t==='source');
        filter();
    }
    function filter() {
        let q = document.getElementById('toolSearch').value.toLowerCase();
        document.querySelectorAll('.item').forEach(i => {
            let n = i.querySelector('.tool-name').innerText.toLowerCase();
            i.style.display = (n.includes(q) && i.dataset.type === view) ? "block" : "none";
        });
    }
    window.onload = () => tab('tool');
</script>
</body>
</html>
