Nastavení projektu
================

Přejděte do nabídky **Nastavení** a poté vlevo vyberte možnost **Nastavení projektu**.

![Nastavení projektu](screenshots/project-settings.png)

### Výchozí sloupce pro nové projekty

Zde můžete změnit výchozí názvy sloupců.
Toto je užitečné, pokud vždy vytváříte projekty se stejnými sloupci.

Každé jméno sloupce musí být odděleno čárkou.

Ve výchozím nastavení Kanboard používá tyto názvy sloupců: Nevyřízené, Připraveno, V řešení a Dokončeno.

### Výchozí kategorie pro nové projekty

Kategorie nejsou globální pro aplikaci, ale jsou připojeny k projektu.
Každý projekt může mít různé kategorie.

Pokud však vždy vytvoříte stejné kategorie pro všechny vaše projekty, můžete zde definovat seznam kategorií, které chcete vytvořit automaticky.

### Povolit současně pouze jednu dílčí úlohu pro uživatele

Je-li tato volba povolena, může uživatel pracovat pouze s jednou dílčí úlohou v daném okamžiku.

Pokud má další dílčí úloha stav "probíhající", zobrazí se toto dialogové okno:

![Omezení uživatele dílčího úkolu](screenshots/subtask-user-restriction.png)

### Spustit automaticky sledování času

- Pokud je povoleno, když je stav dílčí úlohy změněn na "probíhající", časovač se spustí automaticky.
- Tuto možnost deaktivujte, pokud nepoužíváte sledování času.

### Zahrnout uzavřené úkoly v kumulativním vývojovém diagramu

- Pokud je povoleno, uzavřené úkoly budou zahrnuty do kumulativního diagramu.
- Pokud je zakázáno, budou zahrnuty pouze otevřené úkoly.
- Tato volba ovlivňuje sloupec "celkem" tabulky "project_daily_column_stats"
