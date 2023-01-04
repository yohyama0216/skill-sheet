# Welcome to Sonic Pi
define :first_base do
  use_synth :bass_foundation
  3.times do
    play 36
    sleep 2
    play 39
    sleep 2
    play 38
    sleep 2
    play 41
    sleep 1
    play 43
    sleep 1
  end
end

## 「花咲く森の道くまさんに出会った」部分の演奏
define :play_last do
  #花咲く
  play :G4
  sleep 0.25
  play :A4
  sleep 0.25
  play :B4
  sleep 0.25
  
  #森の道
  play :C5
  sleep 0.5
  play :G4
  sleep 0.5
  play :E4
  sleep 0.5
  play :C4
  sleep 0.5
  play :A4
  sleep 1.25
  
  #くまさんに
  play :A4
  sleep 0.25
  play :B4
  sleep 0.25
  play :A4
  sleep 0.25
  
  #出会った
  play :G4
  sleep 0.5
  play :F4
  sleep 0.5
  play :E4
  sleep 0.5
  play :D4
  sleep 0.5
  play :C4
  sleep 1.25
end

in_thread do
  first_base
end

in_thread do
  sleep 8
  use_synth :piano
  play 60
end
