<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lojas 20+</title>
  <link rel="icon" type="image/png" href="{{ asset('images/key_icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/grupo20_icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Jost:wght@200;300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

  @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
</head>
<body>

  <!-- ─── HEADER ─── -->
  <header>
    <a href="/" class="logo-text">
        <img src="{{ asset('images/logo.png') }}" alt="Lojas 20+" class="logo-img" style="height: 44px; width: auto;">
    </a>
    <nav>
      <ul>
        <li><a href="#historia">Empresa</a></li>
        <li><a href="#departamentos">Produtos</a></li>
        <li><a href="#foot">Contato</a></li>
      </ul>
    </nav>
  </header>

  <!-- ─── HERO SECTION ─── -->
  <section class="hero">
    <div class="hero-eyebrow">Itacoatiara &middot; Amazonas</div>
    <h1>Muito <em>mais</em><br>pra você</h1>
    <p class="hero-sub">Lingeries, roupas femininas e bijuterias cuidadosamente selecionadas para cada ocasião da sua vida. O estilo que você merece.</p>
    <div class="hero-cta">
      <a href="#departamentos" class="btn-primary">Ver Coleções</a>
      <a href="#historia" class="btn-ghost">Nossa História</a>
    </div>
  </section>

  <!-- ─── DEPARTAMENTOS ─── -->
  <section id="departamentos">
    <div class="reveal">
      <h2 class="section-title">Conheça Nossos<br><em>Departamentos</em></h2>
      <span class="section-rule"></span>
    </div>

    <div class="dept-grid reveal-stagger">

      <!-- Lingeries -->
      <div class="dept-card">
        <img src="{{ asset('images/dept_lingerie.png') }}" alt="Lingeries" class="dept-card-img" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block;">
        <span class="dept-card-tag">Destaque</span>
        <div class="dept-card-body">
          <h2>Lingeries</h2>
          <p>A lingerie vai além de uma simples peça de roupa íntima; ela representa sensualidade, conforto e autoconfiança. Disponível em diversos estilos, tecidos e modelos, desde delicada e romântica até ousada e moderna.</p>
        </div>
      </div>

      <!-- Roupa Feminina -->
      <div class="dept-card">
        <img src="{{ asset('images/dept_roupas.png') }}" alt="Roupas Femininas" class="dept-card-img" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block;">
        <div class="dept-card-body">
          <h2>Roupa Feminina</h2>
          <p>A roupa feminina é uma expressão de estilo, personalidade e conforto. Desde vestidos elegantes a jeans versáteis, a moda feminina evolui constantemente, refletindo tendências e influências culturais.</p>
        </div>
      </div>

      <!-- Bijouteria -->
      <div class="dept-card">
        <img src="{{ asset('images/dept_bijuterias.png') }}" alt="Bijouterias" class="dept-card-img" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block;">
        <div class="dept-card-body">
          <h2>Bijouteria</h2>
          <p>Acessórios versáteis que adicionam charme e personalidade a qualquer look. Brincos, colares, anéis e pulseiras podem transform uma produção simples em algo sofisticado e moderno.</p>
        </div>
      </div>

    </div>
  </section>

  <!-- ─── HISTÓRIA ─── -->
  <section id="historia">
    <div class="historia-inner reveal">
      <p class="section-label">Quem somos</p>
      <h1>Nossa História</h1>
      <div class="historia-divider"></div>
      <p>Lorem ipsum dolor sit amet. Aut impedit quibusdam est inventore praesentium aut nisi obcaecati in autem modi est repellat internos ea expedita perferendis aut accusamus Quis? Et cupiditate labore ut unde excepturi ea harum placeat eum delectus atque est corrupti perspiciatis.</p>
    </div>
  </section>

  <!-- ─── FEED DE FOTOS (INSTAGRAM STYLE) ─── -->
  <section id="comunidade">
    <div class="feed-header reveal">
      <h2><em>Nosso</em> Feed</h2>
    </div>
    
    <section class="splide" id="photo-splide" aria-label="Nosso Feed" style="padding: 0 5vw;">
      <div class="splide__track">
        <ul class="splide__list">
          @if($photos->isEmpty())
            <li class="splide__slide" style="text-align: center; padding: 2rem;">
              <p>Nenhuma foto na galeria.</p>
            </li>
          @else
            @foreach($photos as $photo)
              <li class="splide__slide">
                <div style="background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border); overflow: hidden;">
                  <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->description }}" style="width: 100%; height: auto; aspect-ratio: 4/5; object-fit: cover;">
                </div>
              </li>
            @endforeach
          @endif
        </ul>
      </div>
    </section>
  </section>

  <!-- ─── FOOTER ─── -->
  <section id="foot">
    <footer class="rodape">
      <div class="footer-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Lojas 20+" style="height: 60px; margin-bottom: 0.5rem; opacity: 0.9;">
        <span class="footer-logo-sub" style="font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;">Itacoatiara, AM</span>
      </div>

      <div class="coluna">
        <h3>Departamentos</h3>
        <a href="#departamentos">Lingeries</a>
        <a href="#departamentos">Roupa Feminina</a>
        <a href="#departamentos">Bijouterias</a>
      </div>

      <div class="coluna">
        <h3>Redes Sociais</h3>
        <a href="https://www.facebook.com/share/19LweQyEet/" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
        <a href="https://www.instagram.com/lojas20mais_itacoatiara" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>
      </div>

      <div class="coluna">
        <h3>Contato & Lojas</h3>
        <a href="https://wa.me/5592993332059" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        <p style="margin-top:0.5rem"><i class="fas fa-phone"></i> (92) 93332-0591</p>
        <p style="margin-top: 1rem;">Seg – Sex: 9h às 18h | Sáb: 9h às 18h</p>
        <a href="/login" style="margin-top: 1rem; text-decoration: underline; font-size: 0.8rem;">Acesso Lojista</a>
      </div>

      <div class="footer-bottom">
        <span>© 2026 Lojas 20+</span>
        <span style="font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1rem;">Muito mais pra você.</span>
      </div>
    </footer>
  </section>
</body>
</html>
