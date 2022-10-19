# --headless　ヘッドレスモード
# --disable-gpu グラボない場合
# --dump-dom HTML出力
# --print-to-pdf=C:\headless\pc.pdf PDF出力＋保存パス
# --window-size=375,812 windowサイズ横縦

# サンプル HTML出力
chrome.exe --headless --disable-gpu --dump-dom https://www.umi-pon.jp/ > C:\headless\output.html

# サンプル　スマホ用サイズ画像の出力
chrome.exe --headless --disable-gpu --window-size=375,812 --screenshot=C:\headless\sp.png https://yahoo.co.jp/

# サンプル　PDF出力
chrome.exe --headless --disable-gpu --window-size=1024,768 --print-to-pdf=C:\headless\pc.pdf https://yahoo.co.jp/