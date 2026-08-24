const { chromium } = require('playwright-core');
const fs = require('fs');

(async () => {
    console.log('Launching browser...');
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    
    try {
        // --- 1. SALES EXEC DASHBOARD ---
        const page1 = await context.newPage();
        await page1.goto('http://127.0.0.1:8000/login');
        await page1.fill('#email', 'sales@example.com');
        await page1.fill('#password', 'password');
        await page1.click('button[type="submit"]');
        await page1.waitForURL('http://127.0.0.1:8000/dashboard');
        await page1.waitForTimeout(2000); // Wait for Livewire render
        await page1.screenshot({ path: 'sales_dashboard.png', fullPage: true });
        console.log('Sales Exec dashboard screenshot saved.');
        await page1.close();
        
        // --- 2. MANAGER DASHBOARD ---
        // Clear cookies / new context for manager
        const context2 = await browser.newContext();
        const page2 = await context2.newPage();
        await page2.goto('http://127.0.0.1:8000/login');
        await page2.fill('#email', 'manager@example.com');
        await page2.fill('#password', 'password');
        await page2.click('button[type="submit"]');
        await page2.waitForURL('http://127.0.0.1:8000/dashboard');
        await page2.waitForTimeout(2000); // Wait for Livewire render
        await page2.screenshot({ path: 'manager_dashboard.png', fullPage: true });
        console.log('Manager dashboard screenshot saved.');
        
        // --- 3. SEND TO CLIENT BUTTON ---
        await page2.goto('http://127.0.0.1:8000/proposals');
        await page2.waitForTimeout(1000);
        // Find a draft proposal and click it
        await page2.click('table tbody tr:first-child td:last-child a');
        await page2.waitForTimeout(2000);
        
        // Take screenshot before click
        await page2.screenshot({ path: 'proposal_before.png', fullPage: true });
        
        // Click the "Send to Client" button
        const sendBtn = await page2.$('button:has-text("Send to Client")');
        if (sendBtn) {
            console.log('Clicking Send to Client...');
            await sendBtn.click();
            await page2.waitForTimeout(3000); // Wait for request and UI update
            await page2.screenshot({ path: 'proposal_after.png', fullPage: true });
            console.log('Proposal after screenshot saved.');
        } else {
            console.log('Send to Client button not found!');
        }
        
        await page2.close();
    } catch (e) {
        console.error('Error:', e);
    } finally {
        await browser.close();
    }
})();
