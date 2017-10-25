## Errata (Design Decisions)

### Reasoning Behind the Order of Preferred Random Data Sources

The order is:

 1. `libsodium if available`
 2. `fread() /dev/urandom if available`
 3. `mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM)`
 4. `COM('CAPICOM.Utilities.1')->GetRandom()`

If libsodium is available, we get random data from it. This is the preferred
method on all OSes, but libsodium is not very widely installed, so other
fallbacks are available.

Next, we read `/dev/urandom` (if it exists). This is the preferred file to read
for random data for cryptographic purposes for BSD and Linux.

Despite [strongly urging people not to use mcrypt in their projects](https://paragonie.com/blog/2015/05/if-you-re-typing-word-mcrypt-into-your-code-you-re-doing-it-wrong),
because libmcrypt is abandonware and the API puts too much responsibility on the
implementor, we prioritize `mcrypt_create_iv()` with `MCRYPT_DEV_URANDOM` above
the remaining implementations.

The reason is simple: `mcrypt_create_iv()` is part of PHP's `ext/mcrypt` code,
and is not part `libmcrypt`. It actually does the right thing:

 * On Unix-based operating systems, it reads from `/dev/urandom`, which unlike `/dev/random`
   is the sane and correct thing to do.
 * On Windows, it reads from `CryptGenRandom`, which is an exclusively Windows
   way to get random bytes.

If we're on Windows and don't have access to `mcrypt`, we use `CAPICOM.Utilities.1`.

As of random_compat 1.3, we no longer fall through to OpenSSL. 
