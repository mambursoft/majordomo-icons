<#
.SYNOPSIS
    Собирает архив dist/icons.tgz для Маркета дополнений MajorDoMo.

.DESCRIPTION
    Внутри архива нет родительской папки: все пути относительны корня MajorDoMo.
    Скрипт собирает временный каталог с нужной структурой и упаковывает его
    через tar (входит в Windows 10/11).

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File build_package.ps1
#>
param(
    [string]$Output = 'dist/icons.tgz'
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$stage = Join-Path $env:TEMP ('majordomo_icons_build_' + [guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Force -Path $stage | Out-Null

$rootFiles = @(
    'install.php',
    'modinfo.json',
    'module.xml',
    'README.md',
    'CHANGELOG.md',
    'LICENSE.md',
    'docs'
)
foreach ($item in $rootFiles) {
    $source = Join-Path $root $item
    if (-not (Test-Path $source)) {
        throw "Не найден элемент для упаковки: $item"
    }
    Copy-Item -Path $source -Destination (Join-Path $stage $item) -Recurse -Force
}

$mappings = @(
    @{ From = 'src/css';                  To = 'css' },
    @{ From = 'src/3rdparty/fontawesome'; To = '3rdparty/fontawesome' },
    @{ From = 'src/lib';                  To = 'lib' },
    @{ From = 'src/modules';              To = 'modules' },
    @{ From = 'src/templates';            To = 'templates' },
    @{ From = 'languages';                To = 'languages' }
)
foreach ($map in $mappings) {
    $source = Join-Path $root $map.From
    if (-not (Test-Path $source)) {
        throw "Не найден каталог для упаковки: $($map.From)"
    }
    Copy-Item -Path $source -Destination (Join-Path $stage $map.To) -Recurse -Force
}

$outputPath = Join-Path $root $Output
New-Item -ItemType Directory -Force -Path (Split-Path -Parent $outputPath) | Out-Null
if (Test-Path $outputPath) {
    Remove-Item $outputPath -Force
}

Push-Location $stage
try {
    $items = @('css', '3rdparty', 'lib', 'modules', 'templates', 'languages') + $rootFiles
    & tar -czf $outputPath @items
    if ($LASTEXITCODE -ne 0) {
        throw "tar завершился с кодом $LASTEXITCODE"
    }
} finally {
    Pop-Location
    Remove-Item $stage -Recurse -Force -ErrorAction SilentlyContinue
}

$size = (Get-Item $outputPath).Length
Write-Host "Готово: $outputPath ($size байт)"
