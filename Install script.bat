@echo off

:: Define xampp download URL
set "xamppDownloadUrl=https://www.apachefriends.org/index.html"

:: Define the installation directory
set "xamppInstallDir=C:\xampp"

:: Check if xampp is installed
if exist "%xamppInstallDir%" (
    echo xampp is already installed in %xamppInstallDir%.
) else (
    :: Download xampp
    echo Downloading xampp...
    curl -o xampp-installer.exe "%xamppDownloadUrl%"

    :: Install XAMPP
    echo Installing XAMPP...
    start /wait xampp-installer.exe /S

    :: Check the installation result
    if exist "%xamppInstallDir%" (
        echo xampp has been successfully installed in %xamppInstallDir%.
    ) else (
        echo Error during xampp installation.
    )
)

:: GitHub repository URL
set "githubRepoUrl=https://github.com/SYNdiCull/Project-Emerald.git"

:: Define the target installation directory
set "targetDirectory=C:\xampp\htdocs\Project-Emerald"

:: Check if the target directory exists
if exist "%targetDirectory%" (
    echo The target directory already exists.
    exit /b
)

:: Clone the repository
git clone "%githubRepoUrl%" "%targetDirectory%"

:: Check if the clone was successful
if %errorlevel% neq 0 (
    echo Error during installation. Please check the repository URL.
    exit /b
)

echo Project Emerald has been successfully installed.