import subprocess
import re
import sys
import argparse
from typing import Match

# Conventional commit pattern
CONVENTIONAL_COMMIT_PATTERN: str = r"^(build|chore|ci|docs|feat|fix|perf|refactor|revert|style|test)(\([a-z0-9-]+\))?!?: .{1,100}"


def get_commit_message(commit_hash: str) -> str:
    """Get the commit message for a given commit hash."""
    try:
        result: subprocess.CompletedProcess = subprocess.run(
            ["git", "show", "-s", "--format=%B", commit_hash],
            capture_output=True,
            text=True,
            check=True,
        )
        return result.stdout.strip()
    except subprocess.CalledProcessError as e:
        print(f"Error retrieving commit message: {e}")
        sys.exit(1)


def check_commit_message(
    message: str, pattern: str = CONVENTIONAL_COMMIT_PATTERN
) -> bool:
    """Check if commit message follows conventional commit format."""
    first_line: str = message.split("\n")[0]
    match: Match[str] | None = re.match(pattern, first_line)
    return bool(match)


def check_commit_range(base_ref: str, head_ref: str) -> list[dict[str, str]]:
    """Check all commits in a range for compliance."""
    try:
        result: subprocess.CompletedProcess = subprocess.run(
            ["git", "log", "--format=%H", f"{base_ref}..{head_ref}"],
            capture_output=True,
            text=True,
            check=True,
        )
        commit_hashes: list[str] = result.stdout.strip().split("\n")

        # Filter out empty lines
        commit_hashes = [hash for hash in commit_hashes if hash]

        non_compliant: list[dict[str, str]] = []
        for commit_hash in commit_hashes:
            message: str = get_commit_message(commit_hash)
            if not check_commit_message(message):
                non_compliant.append(
                    {"hash": commit_hash, "message": message.split("\n")[0]}
                )

        return non_compliant
    except subprocess.CalledProcessError as e:
        print(f"Error checking commit range: {e}")
        sys.exit(1)


def main() -> None:
    parser: argparse.ArgumentParser = argparse.ArgumentParser(
        description="Check conventional commit compliance"
    )
    parser.add_argument(
        "--base", required=True, help="Base ref (starting commit, exclusive)"
    )
    parser.add_argument(
        "--head", required=True, help="Head ref (ending commit, inclusive)"
    )
    args: argparse.Namespace = parser.parse_args()

    non_compliant: list[dict[str, str]] = check_commit_range(args.base, args.head)

    if non_compliant:
        print("The following commits do not follow the conventional commit format:")
        for commit in non_compliant:
            print(f"- {commit['hash'][:8]}: {commit['message']}")
        print("\nPlease ensure your commit messages follow the format:")
        print("type(scope): subject")
        print(
            "\nWhere type is one of: build, chore, ci, docs, feat, fix, perf, refactor, revert, style, test"
        )
        sys.exit(1)
    else:
        print("All commits follow the conventional commit format!")
        sys.exit(0)


if __name__ == "__main__":
    main()
