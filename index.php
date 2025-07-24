<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername   = "localhost";
$username     = "root";
$password     = "";
$dbname       = "sozluk";

// Bağlantıyı aç
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Değişkenler
$arama          = "";
$definition     = null;
$suggestions    = [];
$showResults    = false;
// Yeni: öneriden tıklayıp tıklamadığını anlamak için
$fromSuggest    = isset($_GET['suggest']);

// Arama formu gönderildiyse
if (isset($_GET['arama']) && trim($_GET['arama']) !== '') {
    $showResults = true;
    $arama = $conn->real_escape_string($_GET['arama']);

    // 1) Önce tam eşleşme ara
    $sqlExact = "SELECT * 
                   FROM dictionary 
                  WHERE name = '$arama' 
                  LIMIT 1";
    $resExact = $conn->query($sqlExact);
    if ($resExact && $resExact->num_rows === 1) {
        $definition = $resExact->fetch_assoc();
    } else {
        // 2) Tam eşleşme yoksa öneri getir
        //    Burada LIMIT 10, isminde arama terimi geçen ilk 10 kelimeyi alıyoruz
        $sqlLike = "
            SELECT name
              FROM dictionary
             WHERE name LIKE '%$arama%'
          ORDER BY name ASC
             LIMIT 10
        ";
        $resLike = $conn->query($sqlLike);
        if ($resLike) {
            while ($r = $resLike->fetch_assoc()) {
                $suggestions[] = $r['name'];
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


  <meta charset="UTF-8">
  <title>Türkçe Sözlük</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">

<div class="heart tr">💖</div>
<div class="heart bl">💕</div>
<div class="heart br">💘</div>

    <div class="header-icon">B</div>
 
    
    <h1>Türkçe Sözlük</h1>
    <p class="slogan">Aradığınız her şey...</p>


    <!-- Arama Formu -->
   <form method="get" id="aramaForm" class="d-flex justify-content-center mt-4">
  <input
    type="text"
    name="arama"
    id="arama"
    class="form-control me-2 w-50"
    placeholder="Kelime ara..."
    value="<?php echo htmlspecialchars($arama); ?>"
    autocomplete="off"
    required>
  <button type="submit" class="btn btn-pink">
    <i class="bi bi-search"></i> Ara
  </button>
</form>


    <!-- 1) Tam eşleşme bulunduysa açıklama -->
    <?php if ($definition): ?>
      <div id="sonuclar">
        <h2><?php echo htmlspecialchars($definition['name']); ?></h2>
        <p><?php echo htmlspecialchars($definition['description']); ?></p>
      <?php if ($definition || (!empty($suggestions) && !$fromSuggest) || $showResults): ?>
 
  </div>
<?php endif; ?>

      </div>

    <!-- 2) Öneri varsa ve bu öneriden gelinmemişse başlık + liste -->
    <?php elseif (!empty($suggestions) && !$fromSuggest): ?>
      <div id="sonuclar">
        <p>Kayıt bulunamadı. Şunu mu demek istediniz?</p>
        <ul>
          <?php foreach ($suggestions as $s): ?>
            <li>
              <!-- tıklanınca arama parametresine &suggest=1 ekliyoruz -->
              <a href="?arama=<?php echo urlencode($s); ?>&suggest=1">
                <?php echo htmlspecialchars($s); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ($definition || (!empty($suggestions) && !$fromSuggest) || $showResults): ?>
  
  </div>
<?php endif; ?>

      </div>

    <!-- 3) Arama yapıldı ama ne tam eşleşme ne öneri var -->
    <?php elseif ($showResults): ?>
      <div id="sonuclar">
        <p>Kayıt bulunamadı.</p>
      </div>
    <?php endif; ?>
<?php if ($definition || (!empty($suggestions) && !$fromSuggest) || $showResults): ?>
  <div class="text-center mt-4 mb-3">
  <a href="index.php" class="btn btn-outline-danger btn-lg rounded-pill fw-bold">Baş Sayfaya Dön</a>

</div>
</div>

  </div>
<?php endif; ?>

  </div>
  <script src="script.js"></script>
</body>
</html>
