$pluginName = "mtts-lms"
$sourceDir = "."
$exclude = @(".git", ".gitignore", "build.ps1", "*.zip", ".vscode", "node_modules")
$zipFile = "..\$pluginName.zip"

if (Test-Path $zipFile) {
    Remove-Item $zipFile
}

Write-Host "Creating zip archive for $pluginName in parent directory..."

Compress-Archive -Path $sourceDir -DestinationPath $zipFile -CompressionLevel Optimal

Write-Host "Build complete: $zipFile"
