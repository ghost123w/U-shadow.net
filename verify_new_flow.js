const { chromium } = require('playwright');
const assert = require('assert');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();
    const baseUrl = 'http://localhost:8080';

    try {
        console.log('Testing Index Page...');
        await page.goto(`${baseUrl}/index.php`);
        const title = await page.title();
        console.log('Title:', title);
        assert(title.includes('Professional Link Hub'));

        console.log('Testing User Login...');
        await page.fill('input[name="username"]', 'secure_user');
        await page.fill('input[name="password"]', 'secure_pass123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard.php');
        console.log('Logged in to Dashboard');

        console.log('Testing Link Generation & Redirect...');
        const tiktokLink = await page.inputValue('input[value*="tiktok.php"]');
        console.log('TikTok Link:', tiktokLink);

        // Follow the link
        await page.goto(tiktokLink);
        // It should redirect to google (as per template.php)
        await page.waitForTimeout(1000);
        const currentUrl = page.url();
        console.log('Redirected to:', currentUrl);
        assert(currentUrl.includes('google.com'));

        console.log('Testing Analytics Capture...');
        await page.goto(`${baseUrl}/dashboard.php`);
        const analyticsTable = await page.innerText('table');
        console.log('Analytics Table Content:', analyticsTable);
        assert(analyticsTable.includes('tiktok'));

        console.log('Testing Admin Access...');
        await page.goto(`${baseUrl}/admin.php`);
        await page.fill('input[name="username"]', 'admin');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/admin_categories.php');
        console.log('Logged in to Admin Hub');

        await page.screenshot({ path: 'new_flow_verification.png', fullPage: true });
        console.log('Verification successful!');

    } catch (error) {
        console.error('Verification failed:', error);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
