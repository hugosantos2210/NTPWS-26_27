const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');
const { spawn } = require('child_process');

const BASE_URL = 'http://127.0.0.1:8000';
const RECORDINGS_DIR = path.resolve(__dirname, '../public/videos');
const PHP_EXE = 'C:\\Users\\gf262\\.config\\herd\\bin\\php84\\php.exe';
const ROUTER_SCRIPT = 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';

if (!fs.existsSync(RECORDINGS_DIR)) {
  fs.mkdirSync(RECORDINGS_DIR, { recursive: true });
}

// Inject floating HUD overlay
async function updateHUD(page, chapter, title, subtitle) {
  await page.evaluate(({ chapter, title, subtitle }) => {
    let hud = document.getElementById('pawsafe-hud');
    if (!hud) {
      hud = document.createElement('div');
      hud.id = 'pawsafe-hud';
      hud.style.cssText = `
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999999;
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #ffffff;
        padding: 12px 24px;
        border-radius: 9999px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        max-width: 90vw;
      `;
      document.body.appendChild(hud);
    }
    hud.innerHTML = `
      <div style="display: flex; align-items: center; gap: 8px;">
        <span style="background: #2563eb; color: #ffffff; font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 9999px; letter-spacing: 0.05em; text-transform: uppercase;">
          ${chapter}
        </span>
        <div style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 10px #22c55e;"></div>
      </div>
      <div style="display: flex; flex-direction: column;">
        <span style="font-weight: 700; font-size: 15px; color: #f8fafc; line-height: 1.2;">${title}</span>
        ${subtitle ? `<span style="font-size: 12.5px; color: #94a3b8; margin-top: 2px;">${subtitle}</span>` : ''}
      </div>
    `;
    hud.style.opacity = '1';
    hud.style.transform = 'translateX(-50%) translateY(0)';
  }, { chapter, title, subtitle });
}

// Full-screen intro / outro splash card
async function showSplash(page, title, subtitle, badges = []) {
  await page.evaluate(({ title, subtitle, badges }) => {
    let splash = document.getElementById('pawsafe-splash');
    if (!splash) {
      splash = document.createElement('div');
      splash.id = 'pawsafe-splash';
      splash.style.cssText = `
        position: fixed;
        inset: 0;
        z-index: 10000000;
        background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        transition: opacity 0.6s ease-in-out;
      `;
      document.body.appendChild(splash);
    }
    splash.innerHTML = `
      <div style="font-size: 54px; margin-bottom: 12px; filter: drop-shadow(0 4px 12px rgba(37,99,235,0.4));">🐾</div>
      <h1 style="font-size: 38px; font-weight: 800; margin: 0 0 10px 0; background: linear-gradient(135deg, #60a5fa, #38bdf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        ${title}
      </h1>
      <p style="font-size: 18px; color: #94a3b8; max-width: 650px; margin: 0 0 28px 0; line-height: 1.5;">
        ${subtitle}
      </p>
      <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; max-width: 700px;">
        ${badges.map(b => `<span style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #e2e8f0; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 9999px;">${b}</span>`).join('')}
      </div>
    `;
    splash.style.opacity = '1';
    splash.style.pointerEvents = 'auto';
  }, { title, subtitle, badges });
}

async function hideSplash(page) {
  await page.evaluate(() => {
    const splash = document.getElementById('pawsafe-splash');
    if (splash) {
      splash.style.opacity = '0';
      splash.style.pointerEvents = 'none';
      setTimeout(() => splash.remove(), 600);
    }
  });
}

// Smooth scrolling helper
async function smoothScroll(page, distance, steps = 15, delay = 35) {
  for (let i = 0; i < steps; i++) {
    await page.evaluate((d) => window.scrollBy(0, d), distance / steps);
    await page.waitForTimeout(delay);
  }
}

async function scrollToTop(page, steps = 10, delay = 30) {
  const current = await page.evaluate(() => window.scrollY);
  for (let i = 0; i < steps; i++) {
    await page.evaluate(
      ({ pos, step, total }) => window.scrollTo(0, pos * (1 - step / total)),
      { pos: current, step: i + 1, total: steps }
    );
    await page.waitForTimeout(delay);
  }
}

async function humanType(page, selector, text) {
  await page.click(selector);
  for (const char of text) {
    await page.keyboard.type(char, { delay: Math.floor(Math.random() * 25) + 20 });
  }
}

async function waitForServer(url, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    try {
      const res = await fetch(url);
      if (res.status === 200 || res.status === 302) return true;
    } catch (e) {
      // wait a bit
    }
    await new Promise((r) => setTimeout(r, 400));
  }
  throw new Error(`Timeout aguardando servidor em ${url}`);
}

(async () => {
  console.log('🚀 Iniciando servidor PHP integrado para gravação...');
  const appRoot = path.resolve(__dirname, '..');
  const phpServer = spawn(PHP_EXE, ['-S', '127.0.0.1:8000', ROUTER_SCRIPT], {
    cwd: appRoot,
    stdio: 'ignore'
  });

  try {
    await waitForServer(BASE_URL);
    console.log('✅ Servidor PHP activo em 127.0.0.1:8000!');

    console.log('🚀 Iniciando gravação do vídeo do PawSafe com Chrome...');
    const browser = await chromium.launch({
      executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
      headless: true,
    });

    const context = await browser.newContext({
      viewport: { width: 1366, height: 768 },
      recordVideo: {
        dir: RECORDINGS_DIR,
        size: { width: 1366, height: 768 }
      }
    });

    const page = await context.newPage();

    // ══════════════════════════════════════════════════════════════════
    // INTRO SPLASH
    // ══════════════════════════════════════════════════════════════════
    await page.goto(BASE_URL);
    await showSplash(
      page,
      'PawSafe — Plataforma de Adoção & Gestão',
      'Demonstração completa de todas as funcionalidades e páginas da aplicação',
      ['Multilíngue (PT/EN/HR)', 'Autenticação 2FA', 'Painel de Administração', 'APIs de Raças (Cães e Gatos)', 'Gestão de Notícias Quill.js']
    );
    await page.waitForTimeout(2800);
    await hideSplash(page);
    await page.waitForTimeout(500);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 1: HOMEPAGE (PT)
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '01 / 15', 'Página Principal (Português)', 'Apresentação do abrigo, estatísticas e animais em destaque');
    await page.waitForTimeout(1000);
    await smoothScroll(page, 450);
    await page.waitForTimeout(700);
    await smoothScroll(page, 550);
    await page.waitForTimeout(800);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await scrollToTop(page);
    await page.waitForTimeout(600);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 2: MULTILINGUAL - ENGLISH & CROATIAN
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '02 / 15', 'Internacionalização — Inglês (EN)', 'Alternância fluida de idioma com cabeçalhos e conteúdos traduzidos');
    await page.goto(`${BASE_URL}/locale/en`);
    await updateHUD(page, '02 / 15', 'Internacionalização — Inglês (EN)', '"Find your new best friend" • "Adopt a Pet" • "About us"');
    await page.waitForTimeout(900);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await scrollToTop(page);

    await updateHUD(page, '03 / 15', 'Internacionalização — Croata (HR)', 'Suporte nativo a Hrvatski para toda a plataforma');
    await page.goto(`${BASE_URL}/locale/hr`);
    await updateHUD(page, '03 / 15', 'Internacionalização — Croata (HR)', '"Pronađite svog novog najboljeg prijatelja" • "Udomi ljubimca"');
    await page.waitForTimeout(900);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await scrollToTop(page);

    // Voltar para Português
    await page.goto(`${BASE_URL}/locale/pt`);
    await page.waitForTimeout(500);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 3: ABOUT PAGE (/about)
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '04 / 15', 'Página "Sobre Nós"', 'História do abrigo, missão, valores, equipa e processo de adoção');
    await page.goto(`${BASE_URL}/about`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await scrollToTop(page);
    await page.waitForTimeout(500);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 4: NEWS INDEX & ARTICLE DETAIL
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '05 / 15', 'Secção de Notícias & Blog', 'Artigos de sensibilização, categorias e integração com API REST');
    await page.goto(`${BASE_URL}/news`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(700);

    await updateHUD(page, '06 / 15', 'Detalhe do Artigo de Notícia', 'Conteúdo formatado, autor, data, artigos relacionados e API REST');
    const firstArticleLink = await page.$('.news-card a, article a');
    if (firstArticleLink) {
      await firstArticleLink.click();
    } else {
      await page.goto(`${BASE_URL}/news/max-found-his-forever-home`);
    }
    await page.waitForLoadState('domcontentloaded');
    await updateHUD(page, '06 / 15', 'Detalhe do Artigo de Notícia', 'Visualização completa da notícia com badges e widgets');
    await page.waitForTimeout(900);
    await smoothScroll(page, 500);
    await page.waitForTimeout(800);
    await smoothScroll(page, 400);
    await page.waitForTimeout(800);
    await scrollToTop(page);
    await page.waitForTimeout(500);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 5: PETS CATALOGUE & FILTERS
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '07 / 15', 'Catálogo de Animais para Adoção', 'Filtros dinâmicos por espécie (Cão/Gato), género e porte');
    await page.goto(`${BASE_URL}/pets`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 350);
    await page.waitForTimeout(700);

    await updateHUD(page, '07 / 15', 'Filtragem Dinâmica', 'Filtro por Cães (Dogs) e busca instantânea');
    await page.selectOption('select[name="species"]', 'dog').catch(() => {});
    await page.click('button[type="submit"]').catch(() => {});
    await page.waitForLoadState('domcontentloaded');
    await updateHUD(page, '07 / 15', 'Resultados Filtrados', 'Apresentação personalizada dos cães disponíveis para adoção');
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(800);
    await scrollToTop(page);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 6: PET DETAIL & EXTERNAL BREED API
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '08 / 15', 'Perfil do Animal & API de Raças', 'Dados do animal e integração em tempo real com TheDogAPI / TheCatAPI');
    await page.goto(`${BASE_URL}/pets/1`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 500);
    await page.waitForTimeout(1000); // Vista da API externa
    await smoothScroll(page, 400);
    await page.waitForTimeout(1000);
    await scrollToTop(page);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 7: USER AUTHENTICATION - REGISTER & LOGIN
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '09 / 15', 'Registo de Utilizador', 'Criação de nova conta com validação de segurança');
    await page.goto(`${BASE_URL}/register`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 300);
    await page.waitForTimeout(700);

    await updateHUD(page, '10 / 15', 'Início de Sessão (Login)', 'Autenticação segura de utilizador registado');
    await page.goto(`${BASE_URL}/login`);
    await page.waitForTimeout(700);
    await humanType(page, '#email', 'user@pawsafe.com');
    await humanType(page, '#password', 'password123');
    await page.waitForTimeout(400);
    await page.click('button[type="submit"]');
    await page.waitForLoadState('domcontentloaded');

    // ══════════════════════════════════════════════════════════════════
    // SCENE 8: USER PROFILE & TWO-FACTOR AUTHENTICATION (2FA)
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '11 / 15', 'Perfil & Segurança 2FA', 'Painel pessoal do utilizador e histórico de pedidos');
    await page.goto(`${BASE_URL}/profile`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);

    await updateHUD(page, '11 / 15', 'Configuração de 2FA (QR Code)', 'Geração de segredo TOTP e códigos de emergência');
    await page.goto(`${BASE_URL}/two-factor-setup`);
    await page.waitForTimeout(1200);
    await smoothScroll(page, 400);
    await page.waitForTimeout(1000);
    await scrollToTop(page);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 9: ADOPTION SUBMISSION FLOW
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '12 / 15', 'Submissão de Candidatura de Adoção', 'Utilizador autenticado preenche o formulário de motivação');
    await page.goto(`${BASE_URL}/pets/2`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 600);
    await page.waitForTimeout(700);

    const messageBox = await page.$('#message');
    if (messageBox) {
      await humanType(page, '#message', 'Olá equipa PawSafe! Temos um apartamento espaçoso com terraço vedado, experiência com animais e trabalhamos em modelo híbrido. Garantimos muito carinho e passeios diários!');
      await page.waitForTimeout(500);
      await page.click('button[type="submit"]');
      await page.waitForLoadState('domcontentloaded');
      await updateHUD(page, '12 / 15', 'Candidatura Submetida!', 'Mensagem de confirmação e registo imediato no sistema');
      await page.waitForTimeout(1200);
    }

    // Voltar ao perfil para ver o pedido
    await page.goto(`${BASE_URL}/profile`);
    await updateHUD(page, '12 / 15', 'Histórico no Perfil', 'O pedido de adoção aparece como "Pendente" no perfil do utilizador');
    await page.waitForTimeout(900);
    await smoothScroll(page, 550);
    await page.waitForTimeout(1000);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 10: ADMIN DASHBOARD
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '13 / 15', 'Painel de Administração — Dashboard', 'Autenticação como Administrador do sistema');
    // Logout
    await page.evaluate(() => {
      const form = document.querySelector('form[action*="logout"]');
      if (form) form.submit();
    });
    await page.waitForTimeout(600);

    // Login as Admin
    await page.goto(`${BASE_URL}/login`);
    await humanType(page, '#email', 'admin@pawsafe.com');
    await humanType(page, '#password', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('domcontentloaded');

    await page.goto(`${BASE_URL}/admin`);
    await updateHUD(page, '13 / 15', 'Painel de Administração — Dashboard', 'Métricas em tempo real: Animais, Pedidos, Notícias e Utilizadores');
    await page.waitForTimeout(1000);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await smoothScroll(page, 450);
    await page.waitForTimeout(800);
    await scrollToTop(page);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 11: ADMIN NEWS & QUILL.JS EDITOR
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '14 / 15', 'Gestão de Notícias & Editor Quill.js', 'Painel CMS com editor de texto rico, categorias e upload');
    await page.goto(`${BASE_URL}/admin/news`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(700);

    // Criar nova notícia
    await page.goto(`${BASE_URL}/admin/news/create`);
    await updateHUD(page, '14 / 15', 'Novo Artigo — Editor Quill.js', 'Interface de criação com campos estruturados e barra de ferramentas rica');
    await page.waitForTimeout(900);
    await humanType(page, '#title', 'Campanha Solidária de Inverno 2026');
    await page.selectOption('#category_id', { index: 1 }).catch(() => {});
    await humanType(page, '#summary', 'Venha conhecer as iniciativas de apoio ao abrigo neste inverno.');
    await page.waitForTimeout(500);

    const quill = await page.$('.ql-editor');
    if (quill) {
      await quill.click();
      await humanType(page, '.ql-editor', 'Estamos a recolher mantas, agasalhos e ração para manter todos os nossos patudos confortáveis.');
    }
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(800);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 12: ADMIN CATEGORIES
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '14 / 15', 'Gestão de Categorias', 'Organização de tópicos, slugs e contagem de notícias associadas');
    await page.goto(`${BASE_URL}/admin/categories`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 350);
    await page.waitForTimeout(800);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 13: ADMIN PETS & ADOPTIONS
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '14 / 15', 'Gestão de Animais & Adoções', 'Registo completo de animais no abrigo e revisão de pedidos pendentes');
    await page.goto(`${BASE_URL}/admin/pets`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(800);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 14: ADMIN USERS & ROLES
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '14 / 15', 'Gestão de Utilizadores & Funções', 'Controlo de acessos RBAC: Administrador, Editor e Utilizador Comum');
    await page.goto(`${BASE_URL}/admin/users`);
    await page.waitForTimeout(900);
    await smoothScroll(page, 400);
    await page.waitForTimeout(800);

    // ══════════════════════════════════════════════════════════════════
    // SCENE 15: ADMIN MULTILINGUAL & CLOSING
    // ══════════════════════════════════════════════════════════════════
    await updateHUD(page, '15 / 15', 'Admin Multilíngue (Inglês & Croata)', 'Painel de gestão totalmente localizado em todas as línguas');
    await page.goto(`${BASE_URL}/locale/en`);
    await page.goto(`${BASE_URL}/admin`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 350);
    await page.waitForTimeout(700);

    await page.goto(`${BASE_URL}/locale/hr`);
    await page.goto(`${BASE_URL}/admin`);
    await page.waitForTimeout(1000);
    await smoothScroll(page, 350);
    await page.waitForTimeout(700);

    // OUTRO SPLASH
    await showSplash(
      page,
      'PawSafe — Plataforma Completa',
      'Todas as funcionalidades, páginas e requisitos implementados com excelência!',
      ['Frontend Responsivo', 'Localização PT / EN / HR', 'Autenticação 2FA TOTP', 'Integração de APIs REST', 'CMS Quill.js', 'Controlo de Acessos RBAC']
    );
    await page.waitForTimeout(3500);

    console.log('✅ Gravação concluída com sucesso!');

    const video = page.video();
    let videoPath = null;
    if (video) {
      videoPath = await video.path();
    }
    await context.close();
    await browser.close();

    if (videoPath && fs.existsSync(videoPath)) {
      const targetPath = path.resolve(__dirname, '../public/videos/pawsafe-demo.webm');
      fs.copyFileSync(videoPath, targetPath);
      console.log('🎬 Arquivo de vídeo finalizado e gravado em:', targetPath);
      console.log('Tamanho:', (fs.statSync(targetPath).size / (1024 * 1024)).toFixed(2), 'MB');
    }
  } catch (err) {
    console.error('❌ Erro durante a gravação:', err);
  } finally {
    try {
      phpServer.kill();
      console.log('🛑 Servidor PHP finalizado.');
    } catch (e) {}
  }
})();
