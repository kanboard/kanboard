# End-to-end (e2e) tests for web

Browser end-to-end tests for Kanboard, authored with [KaneCLI](https://www.testmuai.com/kane-cli/) by TestMu AI. Each test is a plain-English `_test.md` file; KaneCLI runs it in a real Chrome browser and records each step so later runs replay without re-authoring.

| Test | What it covers |
|---|---|
| `project_board_test.md` | Logging in, creating a project, and opening its board with the default columns |
| `task_test.md` | Adding a task to the Backlog column, commenting on it, and closing it |

They complement the API integration tests in `tests/integration` by going through the web UI.

## Run the tests

The tests expect a fresh instance at `http://localhost:8000` with the default `admin` / `admin` account. Build and run the image from this checkout:

```bash
docker build -t kanboard-e2e .
docker run -d --name kanboard-e2e -p 8000:80 kanboard-e2e
```

Or use the PHP built-in server described in [CONTRIBUTING.md](../../CONTRIBUTING.md) on port 8000.

### With KaneCLI

```bash
npm install -g @testmuai/kane-cli
kane-cli login
kane-cli testmd run e2e/kane/project_board_test.md --agent
kane-cli testmd run e2e/kane/task_test.md --agent
```

### With the exported Playwright code

Each test also has an exported Playwright (JavaScript) version under `output-<test>/playwright-javascript-code/`:

```bash
cd e2e/kane/output-task/playwright-javascript-code
npm install
npx playwright install chromium
node test.js
```

Copy `.env.example` to `.env` and fill in your TestMu AI (LambdaTest) credentials first.

## Editing a test

Edit the step text in the `_test.md` file and run it again with `kane-cli testmd run`. Changed steps (and the steps after them) are re-recorded; unchanged steps replay from `output-<test>/`.
