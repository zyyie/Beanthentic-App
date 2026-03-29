@rem
@rem Gradle startup script for Windows
@rem
@if "%DEBUG%"=="" @echo off

set DIRNAME=%~dp0
if "%DIRNAME%"=="" set DIRNAME=.
set APP_HOME=%DIRNAME%

@rem Add default JVM options here.
set DEFAULT_JVM_OPTS="-Xmx64m" "-Xms64m"

@rem Resolve java.exe: JAVA_HOME, then Android Studio JBR, then PATH
set "JAVA_EXE="
if defined JAVA_HOME if exist "%JAVA_HOME%\bin\java.exe" set "JAVA_EXE=%JAVA_HOME%\bin\java.exe"
if not defined JAVA_EXE if exist "%ProgramFiles%\Android\Android Studio\jbr\bin\java.exe" set "JAVA_EXE=%ProgramFiles%\Android\Android Studio\jbr\bin\java.exe"
if not defined JAVA_EXE if exist "%LocalAppData%\Programs\Android\Android Studio\jbr\bin\java.exe" set "JAVA_EXE=%LocalAppData%\Programs\Android\Android Studio\jbr\bin\java.exe"
if not defined JAVA_EXE (
  set "JAVA_EXE=java.exe"
  %JAVA_EXE% -version >NUL 2>&1
  if %ERRORLEVEL% equ 0 goto execute
  set "JAVA_EXE=javaw.exe"
  %JAVA_EXE% -version >NUL 2>&1
  if %ERRORLEVEL% equ 0 goto execute
  echo ERROR: Java not found. Install Android Studio or JDK 17+, or set JAVA_HOME to a JDK.
  exit /b 1
)

"%JAVA_EXE%" -version >NUL 2>&1
if not %ERRORLEVEL% equ 0 (
  echo ERROR: Java at "%JAVA_EXE%" is not working. Fix JAVA_HOME or reinstall Android Studio.
  exit /b 1
)

:execute
@rem Setup the command line
set CLASSPATH=%APP_HOME%\gradle\wrapper\gradle-wrapper.jar

"%JAVA_EXE%" %DEFAULT_JVM_OPTS% -classpath "%CLASSPATH%" org.gradle.wrapper.GradleWrapperMain %*
