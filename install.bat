@echo off

set destination=
if [%1]==[] (
  set destination="%PROGRAMFILES%\bee"
) else (
  set destination="%1\bee"
)

echo Installing bee in %destination%
xcopy /E /I /Y /Q * %destination%
