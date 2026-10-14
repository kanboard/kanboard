import testmu, { resolveVar, setVar } from '@testmuai/playwright-bindings';
import { expect } from '@playwright/test';

testmu.configure({
  "build": "2cef38f2-2e7f-4274-a75c-53e4dd5901ae",
  "name": "Create and Verify New Project Board",
  "tcId": "TC-185",
  "network": (process.env.NETWORK ?? "false").toLowerCase() === "true",
  "autoHealVersion": "AH2",
  "defaultActionTimeoutMs": 10000,
  "defaultNavigationTimeoutMs": 60000,
  "kaneRunV4": true
});

testmu.test("Create and Verify New Project Board", async (page) => {
  let element_0, element_1, element_3, element_2, element_5, element_6;
  await testmu.step("Navigate to http://localhost:8000/login", { instructionId: "36ae81a8-7060-4d3c-8d88-3382a5106c14" }, async () => {
    await page.goto("http://localhost:8000/login");
  });
  
  await testmu.step("Waiting for the queued plan to run", { instructionId: "550c6015-d223-4a8f-818b-0e684a8dd783" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"admin\" into \"Username\"", { instructionId: "24ea5464-96de-40f9-a105-8a78499bcc87" }, async () => {
    element_0 = page.locator("internal:role=textbox[name=\"Username\"i]");
    await element_0.click();
    await element_0.fill("admin");
  });
  
  await testmu.step("Waiting for the plan queue to run", { instructionId: "77d0e307-703a-418a-97de-f1e15dd84a91" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"admin\" into \"Password\"", { instructionId: "d783996d-3f83-451e-a43e-db52dca6b7df" }, async () => {
    element_1 = page.locator("internal:role=textbox[name=\"Password\"i]");
    await element_1.click();
    await element_1.fill("admin");
  });
  
  await testmu.step("Clicking Sign in button", { instructionId: "69cb1976-ee15-4452-ab9c-da50269b5469" }, async () => {
    element_3 = page.locator("internal:role=button[name=\"Sign in\"i]");
    await element_3.click();
  });
  
  await testmu.step("Waiting for the dashboard page to finish loading", { instructionId: "7820325b-374c-44ac-95d1-b00c73790768" }, async () => {
    await page.waitForTimeout(800);
  });
  
  await testmu.step("Reading the current page URL", { instructionId: "4ebf6f6f-5d1c-4752-bd36-530b9302fedc" }, async () => {
    setVar("dashboard_url_check", page.url());
  });
  
  await testmu.step("Extracting the current page title", { instructionId: "f5c61366-6873-45ba-87c8-efb64d297dd8" }, async () => {
    setVar("dashboard_for_admin_check", await page.title());
  });
  
  await testmu.step("Assertion check", { instructionId: "59d6bf19-6489-42ac-8a89-17f35777c5e0" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "the URL contains \"/dashboard\"", "passed": true, "operator": "contains", "transforms": [], "json_path": null, "expected": "/dashboard", "extracted_value": "{{dashboard_url_check}}", "store_key": "dashboard_url_check", "variable_refs": {"{{dashboard_url_check}}": "http://localhost:8000/dashboard"}}, {"description": "the page shows \"Dashboard for admin\"", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "Dashboard for admin", "extracted_value": "{{dashboard_for_admin_check}}", "store_key": "dashboard_for_admin_check", "variable_refs": {"{{dashboard_for_admin_check}}": "Dashboard for admin"}}], "sub_checks": [{"description": "the URL contains \"/dashboard\"", "store_key": "dashboard_url_check", "expected_value": "/dashboard", "extracted_value": "{{dashboard_url_check}}", "operator": "contains", "transforms": []}, {"description": "the page shows \"Dashboard for admin\"", "store_key": "dashboard_for_admin_check", "expected_value": "Dashboard for admin", "extracted_value": "{{dashboard_for_admin_check}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the URL contains \"/dashboard\" and the page shows \"Dashboard for admin\""});
  });
  
  await testmu.step("Navigate to http://localhost:8000/project/create", { instructionId: "b55c0fb4-fc90-48af-8d88-40023937e11f" }, async () => {
    await page.goto("http://localhost:8000/project/create");
  });
  
  await testmu.step("Waiting for the plan to finish queueing", { instructionId: "8fee333f-24f9-4441-935d-c8094498903a" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Website Redesign\" into \"Name\"", { instructionId: "495a2699-ab3a-4474-ab87-b78255edc856" }, async () => {
    element_2 = page.locator("internal:role=textbox[name=\"Name\"i]");
    await element_2.click();
    await element_2.fill("Website Redesign");
  });
  
  await testmu.step("Click \"Save\"", { instructionId: "6ca4219c-9450-454f-8d84-bf16d0766e63" }, async () => {
    element_5 = page.locator("internal:role=button[name=\"Save\"i]");
    await element_5.click();
  });
  
  await testmu.step("Extracting the current page title", { instructionId: "6b73cd7d-8e81-49e8-bb7b-4b8f40e335c0" }, async () => {
    setVar("project_title_website_redesign", await page.title());
  });
  
  await testmu.step("whether the project creation success message is visible on the page", { instructionId: "79bfead9-418e-4cca-b875-d3ade5044ce5" }, async () => {
    setVar("project_created_success_message", await testmu.visionQuery(page, "whether the project creation success message is visible on the page", ""));
  });
  
  await testmu.step("Assertion check", { instructionId: "f47fef7b-a2dd-441f-bc90-9eecd175bf73" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The page shows \"Your project has been created successfully.\"", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{project_created_success_message}}", "store_key": "project_created_success_message", "variable_refs": {"{{project_created_success_message}}": "true"}}, {"description": "The page shows the project title \"Website Redesign\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "Website Redesign", "extracted_value": "{{project_title_website_redesign}}", "store_key": "project_title_website_redesign", "variable_refs": {"{{project_title_website_redesign}}": "Website Redesign"}}], "sub_checks": [{"description": "The page shows \"Your project has been created successfully.\"", "store_key": "project_created_success_message", "expected_value": "true", "extracted_value": "{{project_created_success_message}}", "operator": "equals", "transforms": []}, {"description": "The page shows the project title \"Website Redesign\".", "store_key": "project_title_website_redesign", "expected_value": "Website Redesign", "extracted_value": "{{project_title_website_redesign}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "The page shows \"Your project has been created successfully.\" and the project title \"Website Redesign\"."});
  });
  
  await testmu.step("Clicking Board navigation link in the project bar", { instructionId: "c001d99c-211b-4610-aa6a-e0219b16264a" }, async () => {
    element_6 = page.locator("internal:role=link[name=\"Board\"i]");
    await element_6.click();
  });
  
  await testmu.step("Waiting for the board page to finish loading", { instructionId: "60d21160-1c6c-4d58-8eb5-dd6d608d52f1" }, async () => {
    await page.waitForTimeout(800);
  });
  
  await testmu.step("Checking whether the Backlog board column is present", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("backlog_column_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {25: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(25).textContent.trim() === 'Backlog');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=link[name=\"Backlog \uf0d7\"i]"], query: "whether the Backlog board column is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The board shows the column \"Backlog\"." }));
  });
  
  await testmu.step("Checking whether the Ready board column is present", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("ready_column_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {28: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(28).textContent.trim() === 'Ready');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=link[name=\"Ready \uf0d7\"i]"], query: "whether the Ready board column is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The board shows the column \"Ready\"." }));
  });
  
  await testmu.step("Checking whether the Work in progress board column is present", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("work_in_progress_column_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {31: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(31).textContent.trim() === 'Work in progress');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=link[name=\"Work in progress \uf0d7\"i]"], query: "whether the Work in progress board column is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The board shows the column \"Work in progress\"." }));
  });
  
  await testmu.step("Checking whether the Done board column is present", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("done_column_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {34: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(34).textContent.trim() === 'Done');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=link[name=\"Done \uf0d7\"i]"], query: "whether the Done board column is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The board shows the column \"Done\"." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "d55bb278-b982-4960-a5e5-b909c7b2ee0e" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The board shows the column \"Backlog\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{backlog_column_visible}}", "store_key": "backlog_column_visible", "variable_refs": {"{{backlog_column_visible}}": "true"}}, {"description": "The board shows the column \"Ready\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{ready_column_visible}}", "store_key": "ready_column_visible", "variable_refs": {"{{ready_column_visible}}": "true"}}, {"description": "The board shows the column \"Work in progress\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{work_in_progress_column_visible}}", "store_key": "work_in_progress_column_visible", "variable_refs": {"{{work_in_progress_column_visible}}": "true"}}, {"description": "The board shows the column \"Done\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{done_column_visible}}", "store_key": "done_column_visible", "variable_refs": {"{{done_column_visible}}": "true"}}], "sub_checks": [{"description": "The board shows the column \"Backlog\".", "store_key": "backlog_column_visible", "expected_value": "true", "extracted_value": "{{backlog_column_visible}}", "operator": "equals", "transforms": []}, {"description": "The board shows the column \"Ready\".", "store_key": "ready_column_visible", "expected_value": "true", "extracted_value": "{{ready_column_visible}}", "operator": "equals", "transforms": []}, {"description": "The board shows the column \"Work in progress\".", "store_key": "work_in_progress_column_visible", "expected_value": "true", "extracted_value": "{{work_in_progress_column_visible}}", "operator": "equals", "transforms": []}, {"description": "The board shows the column \"Done\".", "store_key": "done_column_visible", "expected_value": "true", "extracted_value": "{{done_column_visible}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the board shows the columns \"Backlog\", \"Ready\", \"Work in progress\" and \"Done\""});
  });
});

testmu.run();