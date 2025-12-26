# Tasks++
### Server-driven interaktivna web aplikacija (Laravel + HTMX)

**Tasks++** je jednostavna, ali moderna To-Do aplikacija koja demonstrira kako se može napraviti brz i interaktivan korisnički interfejs **bez SPA framework-a** (React, Vue…), korišćenjem **HTMX-a** i klasičnog server-side pristupa.

---

## 🎯 Problem koji se rešava

U velikom broju realnih projekata (CRUD aplikacije, admin paneli, forme) **nije neophodan pun SPA**.  
Korišćenje SPA framework-a u tim slučajevima često dovodi do:

- povećane kompleksnosti projekta
- dodatnog build procesa
- potrebe za state management-om
- dupliranja logike (frontend + backend)

Cilj ovog projekta je da pokaže kako se **interaktivnost može postići direktno kroz HTML**, uz minimalan JavaScript i potpunu kontrolu na serverskoj strani.

---

## 💡 Izabrano rešenje – HTMX

**HTMX** omogućava slanje HTTP zahteva direktno iz HTML-a pomoću `hx-*` atributa i dinamičko ažuriranje delova stranice bez potpunog reload-a.

U kombinaciji sa **Laravel-om i Blade template-ima**, dobija se:

- jednostavan i čitljiv kod
- brz razvoj aplikacije
- lako održavanje
- server-driven UI (*HTML over the wire*)

---

## 🧰 Korišćene tehnologije

- **Laravel (PHP)** – backend logika, routing, validacija
- **Blade** – server-side rendering i partial views
- **HTMX** – interaktivnost bez SPA framework-a
- **MySQL** – baza podataka
- **HTML / CSS** – korisnički interfejs

---

## ✨ Funkcionalnosti aplikacije

- Dodavanje taskova bez reload-a stranice
- “Smart add” sintaksa:
    - `!` → high priority
    - `?` → low priority
    - `@YYYY-MM-DD` → rok izvršenja
- Live search i filter (All / Active / Done)
- Obeležavanje taska kao završenog
- Inline edit taska
- Brisanje taska uz mogućnost vraćanja (Undo)
- Prikaz statistike i progresa u realnom vremenu

---

## ⚖️ Poređenje sa alternativama

| Rešenje | Prednosti | Nedostaci |
|------|----------|----------|
| React / Vue | Moćni SPA framework-i | Overkill za jednostavne CRUD aplikacije |
| Livewire | Laravel-native rešenje | Više “magije”, jača vezanost za framework |
| Alpine.js | Lagan JavaScript | I dalje zahteva JS logiku |
| **HTMX** | Minimalizam, čitljiv HTML | Manje pogodan za kompleksan client-state |

HTMX je izabran jer najbolje odgovara **jednostavnim, ali interaktivnim aplikacijama**.

---

## 🚀 Pokretanje projekta (lokalno)

### Preduslovi
- PHP 8+
- Composer
- MySQL

### Instalacija

```bash
git clone https://github.com/jana17547/tasks-htmx.git
cd tasks-htmx
composer install
