<?php
// 1. INICIAR A SESSÃO 
session_start();

// CONEXÃO
include 'confing.inc.php'; 

//  LÓGICA DA SACOLA PHP

// Se não existir a sacola ainda, cria uma vazia
if (!isset($_SESSION['sacola'])) {
    $_SESSION['sacola'] = array();
}

// DETECTA O NOME DO ARQUIVO ATUAL AUTOMATICAMENTE (Corrige o erro 404)
$pagina_atual = $_SERVER['PHP_SELF'];

// A: ADICIONAR PRODUTO NA SACOLA
if (isset($_GET['acao']) && $_GET['acao'] == 'add') {
    // Pega os dados da URL
    $item = array(
        'nome' => $_GET['nome'],
        'preco' => $_GET['preco'],
        'imagem' => $_GET['imagem'],
        'link' => $_GET['link']
    );
    
    // Adiciona no array da sessão
    $_SESSION['sacola'][] = $item;
    
    // Redireciona para a PRÓPRIA PÁGINA
    header("Location: $pagina_atual?open_cart=true");
    exit();
}

//  REMOVER PRODUTO DA SACOLA
if (isset($_GET['acao']) && $_GET['acao'] == 'remove') {
    $id_remocao = $_GET['id'];
    
    // Remove o item pelo índice
    if(isset($_SESSION['sacola'][$id_remocao])){
        unset($_SESSION['sacola'][$id_remocao]);
        // Reorganiza os índices do array
        $_SESSION['sacola'] = array_values($_SESSION['sacola']);
    }
    
    // Redireciona para a PRÓPRIA PÁGINA
    header("Location: $pagina_atual?open_cart=true");
    exit();
}

// - FIM DA LÓGICA DA SACOLA 


// LÓGICA DE FILTRO DE PRODUTOS
$filtro_sql = "";
$titulo_secao = "DESTAQUES DA LOJA"; 

if (isset($_GET['genero'])) {
    $genero = mysqli_real_escape_string($conn, $_GET['genero']);
    $filtro_sql = "WHERE genero = '$genero'";
    
    if ($genero == 'Masculino') { $titulo_secao = "ROUPAS MASCULINAS"; } 
    elseif ($genero == 'Feminino') { $titulo_secao = "ROUPAS FEMININAS"; } 
    else { $titulo_secao = "ROUPAS " . strtoupper($genero); }
}
elseif (isset($_GET['categoria'])) {
    $categoria = mysqli_real_escape_string($conn, $_GET['categoria']);
    $filtro_sql = "WHERE categoria = '$categoria'";
    $titulo_secao = strtoupper($categoria);
}

$sql = "SELECT * FROM produtos $filtro_sql";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundo Gamer Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #121212; color: #ffffff; }
        
        
        header { background-color: #1a1a1a; padding: 20px 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #333; position: relative; }
        .logo { font-size: 24px; font-weight: 800; font-style: italic; color: #ccc; letter-spacing: 1px; cursor: pointer; }
        .logo span { color: #2ecc71; font-size: 14px; background: #333; padding: 2px 5px; margin-left: 5px; }
        
        .header-icons { display: flex; align-items: center; }
        .header-icons i { margin-left: 20px; font-size: 18px; color: #aaa; cursor: pointer; transition: 0.3s; }
        .header-icons i:hover { color: #fff; }

     
        .cart-icon-container { position: relative; cursor: pointer; margin-left: 20px; }
        .cart-count { 
            position: absolute; top: -8px; right: -10px; background-color: #2ecc71; color: #000; 
            font-size: 10px; font-weight: bold; border-radius: 50%; width: 16px; height: 16px; 
            display: flex; align-items: center; justify-content: center; 
        }

      
        nav { background-color: #0f0f0f; padding: 20px 0; text-align: center; border-bottom: 1px solid #222; }
        nav ul { list-style: none; display: flex; justify-content: center; gap: 30px; }
        nav a { position: relative; text-decoration: none; color: #bdc3c7; font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: 5px 10px; transition: all 0.3s ease; }
        nav a:hover { color: #2ecc71; text-shadow: 0 0 8px rgba(46, 204, 113, 0.6); }
        nav a::after { content: ''; position: absolute; width: 0%; height: 2px; bottom: 0; left: 50%; background-color: #2ecc71; transition: all 0.3s ease; box-shadow: 0 0 10px #2ecc71; }
        nav a:hover::after { width: 100%; left: 0; }
       
        
        .hero { 
            background-image: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 100%), url('https://img.goodfon.com/original/2048x1152/f/68/gamer-geymer-naushniki-geympad.jpg'); 
            background-size: cover; background-position: center; background-repeat: no-repeat; height: 500px; 
            display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; 
            transition: background-image 0.5s ease-in-out; 
        }
        .hero-content { display: flex; align-items: center; justify-content: flex-start; width: 80%; max-width: 1200px; z-index: 2; }
        .hero-text h2 { font-size: 60px; font-weight: 300; color: #f0f0f0; line-height: 1; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); }
        .hero-text h1 { font-size: 60px; font-weight: 900; color: #ffffff; line-height: 1; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); }
        .divider { height: 5px; width: 150px; background-color: #2ecc71; margin: 20px 0; box-shadow: 0 0 10px rgba(46, 204, 113, 0.5); }
        .arrow { position: absolute; top: 50%; transform: translateY(-50%); background-color: rgba(51, 51, 51, 0.8); color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; z-index: 10; }
        .arrow:hover { background-color: #2ecc71; color: #000; }
        .arrow-left { left: 30px; }
        .arrow-right { right: 30px; }
        .dots { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background-color: #888; cursor: pointer; transition: 0.3s; }
        .dot.active { background-color: #2ecc71; box-shadow: 0 0 5px #2ecc71; }

        
        .section-title { text-align: center; margin: 40px 0 20px 0; font-size: 1.5rem; color: #fff; text-transform: uppercase; letter-spacing: 2px; }
        .produtos-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .card { background-color: #1a1a1a; width: 250px; border-radius: 8px; overflow: hidden; transition: transform 0.3s ease; display: flex; flex-direction: column; border: 1px solid #333; position: relative; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(46, 204, 113, 0.2); border-color: #2ecc71; }
        .card img { width: 100%; height: 250px; object-fit: cover; border-bottom: 1px solid #333; }
        .card-info { padding: 15px; text-align: left; }
        .card-title { font-size: 14px; color: #e0e0e0; margin-bottom: 5px; line-height: 1.4; height: 40px; overflow: hidden; }
        .card-genero { font-size: 12px; color: #888; margin-bottom: 10px; text-transform: uppercase; }
        .preco-atual { font-size: 20px; font-weight: bold; color: #2ecc71; margin-bottom: 10px; }
        
        .card-actions { display: flex; border-top: 1px solid #333; }
        .btn-comprar { flex: 1; text-align: center; background-color: #f0f0f0; color: #121212; padding: 12px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 12px; transition: background 0.3s; }
        .btn-comprar:hover { background-color: #2ecc71; color: white; }
        
        
        .btn-add-cart { 
            width: 50px; background-color: #222; color: #2ecc71; border: none; cursor: pointer; font-size: 16px; transition: 0.3s; 
            display: flex; align-items: center; justify-content: center; text-decoration: none;
        }
        .btn-add-cart:hover { background-color: #2ecc71; color: #000; }

        
        .cart-modal {
            display: none; position: fixed; z-index: 100; right: 0; top: 0; width: 350px; height: 100%; 
            background-color: #1a1a1a; box-shadow: -5px 0 15px rgba(0,0,0,0.7); padding: 20px; 
            overflow-y: auto; border-left: 2px solid #2ecc71;
        }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .cart-header h2 { font-size: 20px; color: #2ecc71; }
        .close-cart { font-size: 24px; cursor: pointer; color: #888; }
        .close-cart:hover { color: #fff; }
        
        .cart-item { display: flex; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .cart-item-info { flex: 1; }
        .cart-item-title { font-size: 13px; color: #fff; margin-bottom: 5px; }
        .cart-item-price { font-size: 14px; color: #2ecc71; font-weight: bold; }
        .cart-remove { color: #e74c3c; font-size: 12px; cursor: pointer; margin-top: 5px; display: inline-block; text-decoration: none; }
        .cart-empty { text-align: center; color: #888; margin-top: 50px; }
    </style>
</head>
<body>

    <header>
        <div class="logo">WORLD GAMER <span>SHOP</span></div>
        <div class="header-icons">
            <a href="login.php" style="color: inherit;">
                <i class="far fa-user"></i>
            </a>
            
            <div class="cart-icon-container" onclick="toggleCart()">
                <i class="fas fa-shopping-bag" style="color: #aaa; font-size: 18px;"></i>
                <?php if(count($_SESSION['sacola']) > 0): ?>
                    <div class="cart-count" style="display:flex;"><?php echo count($_SESSION['sacola']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="?">Todos</a></li> 
            <li><a href="?categoria=Jogos">Jogos</a></li>
            <li><a href="?genero=Masculino">Roupas Masculinas</a></li>
            <li><a href="?genero=Feminino">Roupas Femininas</a></li>
        </ul>
    </nav>

    <section class="hero" id="heroBanner">
        <div class="arrow arrow-left" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></div>
        <div class="arrow arrow-right" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></div>
        <div class="hero-content">
            <div class="hero-text">
                <h2>HOODIES</h2>
                <div class="divider"></div>
                <h1>GAMER STORE</h1>
                <p style="margin-top: 15px; color: #ccc; font-size: 1.1rem;">Estilo e conforto para sua gameplay.</p>
            </div>
        </div>
        <div class="dots">
            <div class="dot active" onclick="setSlide(0)"></div>
            <div class="dot" onclick="setSlide(1)"></div>
            <div class="dot" onclick="setSlide(2)"></div>
        </div>
    </section>

    <h2 class="section-title"><?php echo $titulo_secao; ?></h2>

    <section class="produtos-container">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while($produto = mysqli_fetch_assoc($result)) {
                
                $p_nome = urlencode($produto['nome']);
                $p_preco = number_format($produto['preco_atual'], 2, ',', '.');
                $p_img = urlencode($produto['imagem']);
                $p_link = urlencode($produto['link_afiliado']);
                
                $link_add = "?acao=add&nome=$p_nome&preco=$p_preco&imagem=$p_img&link=$p_link";
        ?>
            <div class="card">
                <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                <div class="card-info">
                    <h3 class="card-title"><?php echo $produto['nome']; ?></h3>
                    <p class="card-genero">
                        <?php 
                        if(isset($produto['categoria']) && !empty($produto['categoria'])) { echo $produto['categoria']; } 
                        elseif (isset($produto['genero'])) { echo $produto['genero']; } 
                        else { echo 'Geral'; }
                        ?>
                    </p>
                    <p class="preco-atual">R$ <?php echo number_format($produto['preco_atual'], 2, ',', '.'); ?></p>
                </div>
                <div class="card-actions">
                    <a href="<?php echo $produto['link_afiliado']; ?>" target="_blank" class="btn-comprar">Ver na Loja <i class="fas fa-external-link-alt"></i></a>
                    
                    <a href="<?php echo $link_add; ?>" class="btn-add-cart">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<p style='color:white; text-align:center; width:100%; margin-bottom:50px;'>Nenhum produto encontrado.</p>";
        }
        ?>
    </section>

    <div class="cart-modal" id="cartModal">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-bag"></i> MINHA SACOLA</h2>
            <span class="close-cart" onclick="toggleCart()">&times;</span>
        </div>
        
        <div id="cartItemsContainer">
            <?php 
            if (empty($_SESSION['sacola'])) {
                echo '<p class="cart-empty">Sua sacola está vazia.</p>';
            } else {
                foreach ($_SESSION['sacola'] as $index => $item) {
            ?>
                <div class="cart-item">
                    <img src="<?php echo urldecode($item['imagem']); ?>" alt="Produto">
                    <div class="cart-item-info">
                        <div class="cart-item-title"><?php echo urldecode($item['nome']); ?></div>
                        <div class="cart-item-price">R$ <?php echo $item['preco']; ?></div>
                        
                        <a href="<?php echo urldecode($item['link']); ?>" target="_blank" style="color:#2ecc71; font-size:12px; text-decoration:none;">
                            COMPRAR AGORA
                        </a>
                        <br>
                        <a href="?acao=remove&id=<?php echo $index; ?>" class="cart-remove">Remover</a>
                    </div>
                </div>
            <?php 
                } 
            }
            ?>
        </div>
    </div>

    <script>
        const hero = document.getElementById('heroBanner');
        const dots = document.querySelectorAll('.dot');
        const images = [
            'https://img.goodfon.com/original/2048x1152/f/68/gamer-geymer-naushniki-geympad.jpg', 
            'https://images.wallpapersden.com/image/download/cyberpunk-city-night_a2tuaG2UmZqaraWkpJRmbmdlrWZlbWU.jpg', 
            'https://images.wallpapersden.com/image/download/matrix-code-binary_bGhqaWyUmZqaraWkpJRmbmdlrWZlbWU.jpg'
        ];
        let currentSlide = 0;

        function updateSlide() {
            hero.style.backgroundImage = `linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 100%), url('${images[currentSlide]}')`;
            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentSlide].classList.add('active');
        }
        function changeSlide(direction) {
            currentSlide += direction;
            if (currentSlide >= images.length) currentSlide = 0;
            if (currentSlide < 0) currentSlide = images.length - 1;
            updateSlide();
        }
        function setSlide(index) {
            currentSlide = index;
            updateSlide();
        }
        setInterval(() => { changeSlide(1); }, 5000);

        
        function toggleCart() {
            const modal = document.getElementById('cartModal');
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
            } else {
                modal.style.display = 'block';
            }
        }

        // Verifica se o PHP pediu para abrir a sacola
        <?php if(isset($_GET['open_cart']) && $_GET['open_cart'] == 'true'): ?>
            toggleCart(); 
            // Limpa a URL 
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        <?php endif; ?>
    </script>

</body>
</html>