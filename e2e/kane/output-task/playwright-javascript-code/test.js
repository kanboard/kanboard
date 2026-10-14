import testmu, { resolveVar, setVar } from '@testmuai/playwright-bindings';
import { expect } from '@playwright/test';

testmu.configure({
  "build": "e8d64628-dfb5-4524-a754-8787d8d0ddbb",
  "name": "Create and Manage a Task in Project Board",
  "tcId": "TC-186",
  "network": (process.env.NETWORK ?? "false").toLowerCase() === "true",
  "autoHealVersion": "AH2",
  "defaultActionTimeoutMs": 10000,
  "defaultNavigationTimeoutMs": 60000,
  "kaneRunV4": true
});

testmu.test("Create and Manage a Task in Project Board", async (page) => {
  let element_0, element_1, element_5, element_2, element_7, element_8, element_9, element_3, element_11, element_12, element_13, element_4, element_15, element_16, element_17;
  await testmu.step("Navigate to http://localhost:8000/login", { instructionId: "6675f280-5321-4489-9751-ba7496f37cad" }, async () => {
    await page.goto("http://localhost:8000/login");
  });
  
  await testmu.step("Waiting for username and password fields to be filled", { instructionId: "6868f53f-0a1f-44ca-8f19-389ebab71f38" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"admin\" into \"Username\"", { instructionId: "ce5ff80b-7d6a-4c0e-90da-49fca4b36340" }, async () => {
    element_0 = page.locator("internal:role=textbox[name=\"Username\"i]");
    await element_0.click();
    await element_0.fill("admin");
  });
  
  await testmu.step("Entering password in the sign-in form field", { instructionId: "1c27c182-3819-4e23-bc98-0fe60f7c867f" }, async () => {
    element_1 = page.locator("internal:role=textbox[name=\"Password\"i]");
    await element_1.click();
    await element_1.fill("admin");
  });
  
  await testmu.step("Clicking the Sign in submit button", { instructionId: "086b0c26-9739-4d00-96d0-85e2c63dc858" }, async () => {
    element_5 = page.locator("internal:role=button[name=\"Sign in\"i]");
    await element_5.click();
  });
  
  await testmu.step("Waiting for the dashboard page to settle for 1000 ms", { instructionId: "da85de0e-6743-4a2b-87d4-3a3792ee249d" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Reading the current browser URL", { instructionId: "cb61a450-fa41-41ba-b4b6-a302a598afbd" }, async () => {
    setVar("dashboard_url_check", page.url());
  });
  
  await testmu.step("Assertion check", { instructionId: "aa5cab4d-b3e2-41f2-bdd5-f43d51a4612e" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The current browser URL contains \"/dashboard\".", "passed": true, "operator": "contains", "transforms": [], "json_path": null, "expected": "/dashboard", "extracted_value": "{{dashboard_url_check}}", "store_key": "dashboard_url_check", "variable_refs": {"{{dashboard_url_check}}": "http://localhost:8000/dashboard"}}], "sub_checks": [{"description": "The current browser URL contains \"/dashboard\".", "store_key": "dashboard_url_check", "expected_value": "/dashboard", "extracted_value": "{{dashboard_url_check}}", "operator": "contains", "transforms": []}], "composite_operator": "and", "claim": "the URL contains \"/dashboard\""});
  });
  
  await testmu.step("Navigate to http://localhost:8000/project/create", { instructionId: "72f79025-8345-4d55-88fd-f23ac2f9dada" }, async () => {
    await page.goto("http://localhost:8000/project/create");
  });
  
  await testmu.step("Waiting for the plan steps to be queued", { instructionId: "f4503587-4414-429e-998f-dea3b004c9d6" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Launch Plan\" into \"Name\"", { instructionId: "c677c2ef-2efd-4499-aaa4-fc5ef1e44863" }, async () => {
    element_2 = page.locator("internal:role=textbox[name=\"Name\"i]");
    await element_2.click();
    await element_2.fill("Launch Plan");
  });
  
  await testmu.step("Click \"Save\"", { instructionId: "ba45577e-ae52-4b24-9038-04c6044a277c" }, async () => {
    element_7 = page.locator("internal:role=button[name=\"Save\"i]");
    await element_7.click();
  });
  
  await testmu.step("Clicking the Board navigation link in the project menu", { instructionId: "038281d6-524c-4792-84a4-c8074a77d9ad" }, async () => {
    element_8 = page.locator("internal:role=link[name=\"Board\"i]");
    await element_8.click();
  });
  
  for (let _untilRetries = 0; _untilRetries < 10; _untilRetries++) {
    console.log(`[until-loop] iteration ${_untilRetries + 1}/10`);
    await testmu.step("Waiting for the board page to finish loading", { instructionId: "57202136-1fc7-4900-bab9-37cb8a1cc64d" }, async () => {
      await page.waitForTimeout(500);
    });
    await page.waitForTimeout(500);
    if (await testmu.checkUntilCondition(page, "a link labeled \"Backlog\" is visible")) break;
  }
  
  await testmu.step("Checking whether the Backlog column is visible", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("backlog_column_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {23: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(23) !== null && /\\bBacklog\\b/.test(el(23).textContent));\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=columnheader[name=\"Backlog \uf0d7\"i]"], query: "whether the board displays a Backlog column", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "the board shows a \"Backlog\" column" }));
  });
  
  await testmu.step("Assertion check", { instructionId: "ef1aa3dd-a6da-4989-87d6-bda7f7378374" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "the board shows a \"Backlog\" column", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{backlog_column_visible}}", "store_key": "backlog_column_visible", "variable_refs": {"{{backlog_column_visible}}": "true"}}], "sub_checks": [{"description": "the board shows a \"Backlog\" column", "store_key": "backlog_column_visible", "expected_value": "true", "extracted_value": "{{backlog_column_visible}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the board shows a \"Backlog\" column"});
  });
  
  await testmu.step("Clicking Add a new task", { instructionId: "d9d9b100-5320-4943-bd2e-6feff7e9dadb" }, async () => {
    element_9 = page.locator("internal:role=link[name=\"Add a new task\"i] >> nth=0");
    await element_9.click();
  });
  
  await testmu.step("Waiting for the plan to finish queuing", { instructionId: "e0d005b8-fc12-41fb-bba9-64d4a19c3687" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Write landing page copy\" into \"Title\"", { instructionId: "2710f5e3-ed78-4100-a6b2-326f41898116" }, async () => {
    element_3 = page.locator("internal:role=textbox[name=\"Title\"i]");
    await element_3.click();
    await element_3.fill("Write landing page copy");
  });
  
  await testmu.step("Click \"Save\"", { instructionId: "fc6a2083-4100-4ba5-8bc7-5c09038c2036" }, async () => {
    element_11 = page.locator("internal:role=button[name=\"Save\"i]");
    await element_11.click();
  });
  
  await testmu.step("Checking the Backlog task card title", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("backlog_task_card_title_check", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {21: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(21) ? Array.from(el(21).rows).some((row, i) => i > 0 && row.cells[0] && Array.from(row.cells[0].querySelectorAll('a')).some(a => a.textContent.trim() === 'Write landing page copy')) : false);\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["#board"], query: "whether the Backlog column contains a task card titled Write landing page copy", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "A task card titled \"Write landing page copy\" is shown in the \"Backlog\" column." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "dce84442-4a79-444d-b913-b4b6739e7ca3" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "A task card titled \"Write landing page copy\" is shown in the \"Backlog\" column.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{backlog_task_card_title_check}}", "store_key": "backlog_task_card_title_check", "variable_refs": {"{{backlog_task_card_title_check}}": "true"}}], "sub_checks": [{"description": "A task card titled \"Write landing page copy\" is shown in the \"Backlog\" column.", "store_key": "backlog_task_card_title_check", "expected_value": "true", "extracted_value": "{{backlog_task_card_title_check}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "a task card titled \"Write landing page copy\" is shown in the \"Backlog\" column"});
  });
  
  await testmu.step("Clicking Write landing page copy link in Backlog column", { instructionId: "ac8e633d-a139-40c9-aadb-c712304ae02f" }, async () => {
    element_12 = page.locator("internal:role=link[name=\"Write landing page copy\"i]");
    await element_12.click();
  });
  
  await testmu.step("Clicking Add a comment link in the Actions sidebar", { instructionId: "068c8eba-fcfc-4e9a-9806-272ab7701d32" }, async () => {
    element_13 = page.locator("internal:role=link[name=\"Add a comment\"i]");
    await element_13.click();
  });
  
  await testmu.step("Waiting for the plan to run", { instructionId: "9452ba6c-9b96-4fc8-a165-46b02c9e716d" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Draft ready for review\" into \"New comment\"", { instructionId: "d0839901-e6e2-4959-856c-c3d5d0afc991" }, async () => {
    element_4 = page.locator("internal:role=textbox[name=\"New comment\"i]");
    await element_4.click();
    await element_4.fill("Draft ready for review");
  });
  
  await testmu.step("Click \"Save\"", { instructionId: "afcc1b29-99a9-493c-9784-950003804a90" }, async () => {
    element_15 = page.locator("internal:role=button[name=\"Save\"i]");
    await element_15.click();
  });
  
  await testmu.step("Checking whether the task comment is displayed", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("task_comment_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {116: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(116).textContent.trim() === 'Draft ready for review');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:text=\"Draft ready for review\"i"], query: "whether the task page displays the comment Draft ready for review", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The task page shows the comment \"Draft ready for review\"." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "2e2f9681-0ffe-4c6a-8ff3-1324ed4123f8" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The task page shows the comment \"Draft ready for review\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{task_comment_visible}}", "store_key": "task_comment_visible", "variable_refs": {"{{task_comment_visible}}": "true"}}], "sub_checks": [{"description": "The task page shows the comment \"Draft ready for review\".", "store_key": "task_comment_visible", "expected_value": "true", "extracted_value": "{{task_comment_visible}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the task page shows the comment \"Draft ready for review\""});
  });
  
  await testmu.step("Clicking Close this task in the Actions sidebar", { instructionId: "3c2e98a1-cff8-4719-aca9-188d61409dd5" }, async () => {
    element_16 = page.locator("internal:role=link[name=\"Close this task\"i]");
    await element_16.click();
  });
  
  await testmu.step("Clicking the Yes confirmation button", { instructionId: "886600da-75f0-49bf-815f-d2fafc298419" }, async () => {
    element_17 = page.locator("internal:role=button[name=\"Yes\"i]");
    await element_17.click();
  });
  
  await testmu.step("whether the task status is closed", { instructionId: "c961c4e2-e1a6-4d89-973c-a2f7e8edc118" }, async () => {
    setVar("task_closed_status", await testmu.visionQuery(page, "whether the task status is closed", ""));
  });
  
  await testmu.step("Assertion check", { instructionId: "ffef9d22-722c-4022-96ae-d1589a022f2e" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The task page shows that the task is closed.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{task_closed_status}}", "store_key": "task_closed_status", "variable_refs": {"{{task_closed_status}}": "true"}}], "sub_checks": [{"description": "The task page shows that the task is closed.", "store_key": "task_closed_status", "expected_value": "true", "extracted_value": "{{task_closed_status}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the task page shows that the task is closed"});
  });
});

testmu.run();