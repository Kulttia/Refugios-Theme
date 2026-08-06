# Refugios — Tema WordPress

Tema brutalista suizo-retro para [Refugios](https://refugios.co), librería y cafetería especializada en Itagüí, Antioquia. WordPress + WooCommerce.

## Estructura

- Raíz del repo = carpeta del tema (`wp-content/themes/refugios` en el servidor).
- CSS principal: `style.css` (design system propio).
- Tailwind: compilado localmente a `assets/css/tailwind.min.css` (no se usa CDN).

## Desarrollo

Requiere Node 18+.

```bash
npm install
npm run build:css   # compila Tailwind minificado
npm run watch:css   # recompila al editar plantillas
```

Si agregas o cambias clases Tailwind en archivos `.php`, corre `npm run build:css` y commitea el `tailwind.min.css` resultante — el servidor no compila nada.

## Deploy

Deploy automático vía Git en hPanel de Hostinger (Avanzado → GIT), apuntando este repo a `wp-content/themes/refugios`. Cada push a `main` despliega con el webhook configurado. No se suben zips manualmente.
