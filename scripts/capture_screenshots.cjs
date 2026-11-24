const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');
const { spawn } = require('child_process');

const BASE_URL = 'http://127.0.0.1:8000';
const OUTPUT_DIR = path.resolve(__dirname, '../public/screenshots');
const ROOT_OUTPUT_DIR = path.resolve(__dirname, '../screenshots');
const PHP_EXE = 'C:\\Users\\gf262\\.config\\herd\\bin\\php84\\php.exe';
const ROUTER_SCRIPT = 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php';

[OUTPUT_DIR, ROOT_OUTPUT_DIR].forEach((dir) => {
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
});

async function waitForServer(url, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    try {
      const res = await fetch(url);
      if (res.status === 200 || res.status === 302) return true;
    } catch (e) {}
    await new Promise((r) => setTimeout(r, 400));
  }
  throw new Error(`Timeout waiting for server at ${url}`);
}

(async () => {
  console.log('🚀 Starting integrated PHP server...');
  const appRoot = path.resolve(__dirname, '..');
  const phpServer = spawn(PHP_EXE, ['-S', '127.0.0.1:8000', ROUTER_SCRIPT], {
    cwd: appRoot,
    stdio: 'ignore'
  });

  let browser = null;

  try {
    await waitForServer(BASE_URL);
    console.log('✅ PHP server listening on 127.0.0.1:8000');

    browser = await chromium.launch({
      executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
      headless: true,
    });

    const context = await browser.newContext({
      viewport: { width: 1440, height: 900 },
      deviceScaleFactor: 1.5,
    });

    const page = await context.newPage();

    // First visit root to establish session / referer
    console.log('🌐 Visiting Home page first...');
    await page.goto(BASE_URL, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);

    // Switch locale to English
    console.log('🌐 Switching locale to English (en)...');
    await page.goto(`${BASE_URL}/locale/en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);

    const shots = [
      {
        id: '01_home_page.png',
        url: `${BASE_URL}/`,
        title: 'Home Page (English)',
        scroll: 0,
      },
      {
        id: '02_about_page.png',
        url: `${BASE_URL}/about`,
        title: 'About Us Page (English)',
        scroll: 0,
      },
      {
        id: '03_news_page.png',
        url: `${BASE_URL}/news`,
        title: 'News & Blog Page (English)',
        scroll: 0,
      },
      {
        id: '04_pets_catalog.png',
        url: `${BASE_URL}/pets`,
        title: 'Adoptable Pets Catalog (English)',
        scroll: 0,
      },
      {
        id: '05_pet_detail.png',
        url: `${BASE_URL}/pets/1`,
        title: 'Pet Detail & Breed API (English)',
        scroll: 150,
      },
    ];

    for (const item of shots) {
      console.log(`📸 Capturing: ${item.title}...`);
      await page.goto(item.url, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(800);
      if (item.scroll > 0) {
        await page.evaluate((y) => window.scrollTo(0, y), item.scroll);
        await page.waitForTimeout(400);
      }

      const filePathPublic = path.join(OUTPUT_DIR, item.id);
      const filePathRoot = path.join(ROOT_OUTPUT_DIR, item.id);
      await page.screenshot({ path: filePathPublic, fullPage: false });
      fs.copyFileSync(filePathPublic, filePathRoot);
      console.log(`   Saved ${item.id} (${(fs.statSync(filePathPublic).size / 1024).toFixed(1)} KB)`);
    }

    // Capture Admin Dashboard in English
    console.log('📸 Logging into Admin and capturing Admin Dashboard (English)...');
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);
    await page.fill('#email', 'admin@pawsafe.com');
    await page.fill('#password', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    await page.goto(`${BASE_URL}/admin`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(800);

    const adminShotId = '06_admin_dashboard.png';
    const adminPathPublic = path.join(OUTPUT_DIR, adminShotId);
    const adminPathRoot = path.join(ROOT_OUTPUT_DIR, adminShotId);
    await page.screenshot({ path: adminPathPublic, fullPage: false });
    fs.copyFileSync(adminPathPublic, adminPathRoot);
    console.log(`   Saved ${adminShotId} (${(fs.statSync(adminPathPublic).size / 1024).toFixed(1)} KB)`);

    console.log('🎉 All 6 screenshots successfully captured in English!');
  } catch (err) {
    console.error('❌ Error capturing screenshots:', err);
  } finally {
    if (browser) {
      try { await browser.close(); } catch (e) {}
    }
    try {
      phpServer.kill();
      console.log('🛑 PHP server stopped.');
    } catch (e) {}
    process.exit(0);
  }
})();
