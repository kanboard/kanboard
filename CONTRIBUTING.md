# Contributing to Kanboard

Kanboard is a free and open source Kanban project management software that welcomes contributions from the community.

## Project Status

**Important Note**: Kanboard is currently in maintenance mode. This means:

- The original author is not actively developing major new features
- New releases are published regularly based on community contributions
- Pull requests for bug fixes and small improvements are welcomed
- The project follows established guidelines for all contributions

## Ways to Contribute

### 🐛 Bug Reports

If you find a bug, please help us improve Kanboard by reporting it:

1. **Check existing issues** first to avoid duplicates
2. **Use the GitHub issue tracker** to report bugs
3. **Provide detailed information** including:
   - Kanboard version
   - PHP version
   - Web server (Apache, Nginx, etc.)
   - Database type and version
   - Operating system
   - Steps to reproduce the issue
   - Expected vs actual behavior
   - Screenshots, server and browser logs if applicable

### 🔧 Bug Fixes and Small Improvements

We welcome pull requests that fix bugs or make small improvements:

1. **Fork the repository** and create a new branch
2. **Keep changes focused** - one issue per pull request
3. **Test your changes** thoroughly
4. **Follow the existing code style**
5. **Submit a pull request** with a clear description

### 📚 Documentation

Help improve Kanboard's documentation:

- Fix typos or unclear explanations
- Add missing documentation for features
- Translate documentation to other languages
- Improve code comments

### 🌐 Translations

Kanboard supports multiple languages. Help translate the interface:

1. Check the `app/Locale` directory for existing translations
2. Create or update translation files
3. Follow the existing translation format
4. Test your translations in the application

Refer to the [Translation Guide](https://docs.kanboard.org/v1/dev/translations/) for more details.

## Development Setup

### Prerequisites

- PHP 8.1 or higher
- Web server (Apache, Nginx, or PHP built-in server)
- Database (MySQL, PostgreSQL, or SQLite)
- Composer (for dependency management)

### Local Development

1. **Clone the repository**:
   ```bash
   git clone https://github.com/kanboard/kanboard.git
   cd kanboard
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up your environment**:
   - Copy `config.default.php` to `config.php`
   - Configure your database settings
   - Set up your web server to point to the project directory, or use the PHP built-in server:
   ```bash
   php -S localhost:8000 -t .
   ```

4. **Run unit tests** to ensure everything is working:
   ```bash
   make test-sqlite # or make test-mysql, make test-postgresql
   ```

### Testing

- Test your changes in different browsers
- Test with multiple database types (MySQL, PostgreSQL, SQLite)
- Test with different PHP versions if possible
- Ensure existing functionalities are not broken by your changes
- Run the unit tests and integration tests

## Pull Request Guidelines

Before submitting a pull request, please:

1. **Read the pull request template** (`.github/pull_request_template.md`)
2. **Create a focused branch** from `main` for your changes
3. **Write clear commit messages** using the [conventional commit format](https://www.conventionalcommits.org/)
4. **Keep your changes small and focused** - large PRs are harder to review
5. **Test your changes thoroughly** to ensure they work as expected
6. **Ensure your code passes all tests** and does not introduce new issues
7. **Add or update tests** if when appropriate
8. **Review your code for style and quality** before submitting
9. **Update documentation** if needed

## Code Style Guidelines

- Be consistent with existing code style
- Follow PSR-1 and PSR-2 coding standards
- Configure your code editor to use 4 spaces for indentation
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions and methods focused and small
- Use type hints where appropriate

Refer to [Kanboard's coding standards](https://docs.kanboard.org/v1/dev/coding_standards/) for more details.

## Security

Follow [Kanboard's security guidelines](SECURITY.md) when reporting or fixing security issues.

## Resources

- **Official Website**: <https://kanboard.org/>
- **Documentation**: <https://docs.kanboard.org/>
- **Forums**: <https://kanboard.discourse.group/> or <https://github.com/orgs/kanboard/discussions>
- **GitHub Issues**: <https://github.com/kanboard/kanboard/issues>

## License

By contributing to Kanboard, you agree that your contributions will be licensed under the same [MIT License](LICENSE) that covers the project.
