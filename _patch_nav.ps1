# Fix nav dropdown: remove amber, add text-align left
$path = "style.css"
$raw = [System.IO.File]::ReadAllText($path, [System.Text.Encoding]::UTF8)

# Replace amber border-top on level 2 dropdown
$raw = $raw -replace [regex]::Escape("  border-top: 3px solid var(--color-amber); /* acento ambar arriba */"), "  border-top: 2px solid var(--color-brown);"
$raw = $raw -replace [regex]::Escape("  border-top: 3px solid var(--color-amber);`r`n  box-shadow: 5px 5px 0 rgba(78, 52, 46, 0.2);`r`n  z-index: 500;"), "  border-top: 2px solid var(--color-brown);`r`n  box-shadow: 4px 4px 0 rgba(78, 52, 46, 0.18);`r`n  z-index: 500;"
$raw = $raw -replace [regex]::Escape("  border-top: 3px solid var(--color-amber);`r`n  box-shadow: 5px 5px 0 rgba(78, 52, 46, 0.2);`r`n  z-index: 600;"), "  border-top: 2px solid var(--color-brown);`r`n  box-shadow: 4px 4px 0 rgba(78, 52, 46, 0.18);`r`n  z-index: 600;"

# Replace amber border-left on level-2 items with children
$raw = $raw -replace [regex]::Escape("  border-left: 3px solid var(--color-amber);`r`n  padding-left: calc(1rem - 3px);`r`n}"), "  border-left: 3px solid var(--color-brown);`r`n  padding-left: calc(1rem - 3px);`r`n}"

# Replace amber in mobile accordion
$raw = $raw -replace [regex]::Escape("    border-left: 3px solid var(--color-amber) !important;"), "    border-left: 2px solid var(--color-brown) !important;"

# --- Add text-align: left to level 2 items ---
$raw = $raw -replace [regex]::Escape("  padding: 0.55rem 2.5rem 0.55rem 1rem;  /* espacio a la derecha para flecha */"), "  text-align: left;`r`n  padding: 0.55rem 2.25rem 0.55rem 1rem;"

# --- Add text-align: left to level 3 items ---
$raw = $raw -replace [regex]::Escape("  padding: 0.5rem 1rem;`r`n  font-family: var(--font-sans);`r`n  font-size: 0.7rem;`r`n  font-weight: 500;"), "  text-align: left;`r`n  padding: 0.5rem 1rem;`r`n  font-family: var(--font-sans);`r`n  font-size: 0.7rem;`r`n  font-weight: 500;"

# Add background:cream explicitly to level 2 and 3 items for no transparency
$raw = $raw -replace [regex]::Escape("  color: var(--color-brown);`r`n  border-top: 1px solid rgba(78, 52, 46, 0.1);`r`n  transition: background 0.15s, color 0.15s;`r`n  white-space: nowrap;`r`n}`r`n`r`n#primary-navigation>ul>li>ul>li:first-child>a"), "  color: var(--color-brown);`r`n  background: var(--color-cream);`r`n  border-top: 1px solid rgba(78, 52, 46, 0.1);`r`n  transition: background 0.15s, color 0.15s;`r`n  white-space: nowrap;`r`n  position: relative;`r`n}`r`n`r`n#primary-navigation>ul>li>ul>li:first-child>a"

[System.IO.File]::WriteAllText($path, $raw, [System.Text.Encoding]::UTF8)
Write-Host "Done. Amber removed, text-align left added."
