# Nelson-s-Place-Aparthotel---Web
Site oficial do Nelson's Place Apart Hotel — https://nelsonsplace.co.mz

Site estático (HTML/CSS/JS) com um pequeno handler PHP para o formulário de reservas.

## Estrutura

- `index.html` — página principal (slider, quartos, galeria, contacto)
- `Sobre-Nos.html` — sobre nós e mapa
- `booking.php` — recebe o formulário "agendar visita" e envia email
- `js/translations.js` — textos PT/EN (elementos com `data-translate`)
- `css/custom-fixes.css` — personalizações sobre o template
- `css/colors/scheme-01.css` — cor da marca

## Publicação

Copiar o conteúdo da pasta para o servidor Apache/PHP (Plesk). O `.htaccess` configura segurança, cache e compressão.

Os ficheiros do template que o site não usa estão listados no `.gitignore` e não fazem parte do repositório.
