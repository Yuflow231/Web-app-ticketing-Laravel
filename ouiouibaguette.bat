@echo off
setlocal

:ask
set /p confirm="Es-tu sur de vouloir creer un projet dans ce dossier ? (si tu te trompes tu te dermerdes pour tous les fichiers qui trainent) (O/N) :"
if /i "%confirm%"=="O" goto :execute
if /i "%confirm%"=="N" goto :end
echo Individue pas tres intelligent repere. Je t'ai demande de repondre soit par O ou par N. On recommence
goto :ask

:execute
echo Creation du viru... euhhh du projets web :kappa:
start "New Window" cmd /c "npm init --yes"
start "New Window" cmd /c "npm i vite"
start "New Window" cmd /c "npm pkg set scripts.start="vite""
echo Normalement c'est bon
goto :end

:end
echo J'ai fini (ou pas) maintenant je degage (oublie quand meme pas de creer le fichier HTML)
endlocal
pause