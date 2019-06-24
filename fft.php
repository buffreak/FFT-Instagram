<?php
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
require_once('func.php');
echo "Copyright " . date('Y') . " By Teuku Muhammad Rivai fb.com/RivaiWatermark\n";
echo "Opsi Login : 1 = Login Token FB\nInput :";
$opsiLogin = trim(fgets(STDIN));
if ($opsiLogin == '1') {
    echo "Token FB EAA.....\nInput :";
    $username = trim(fgets(STDIN));
} else {
    echo "Masukkan Username..\nInput :";
    $username = trim(fgets(STDIN));
}
if (!file_exists("$username.ig")) {
    if ($opsiLogin == 1) {
        $log = masuk($username);
    } else {
        echo "Masukkan Password..\nInput :";
        $password = trim(fgets(STDIN));
        $log      = masuk2($username, $password);
    }
    if ($log == "data berhasil diinput") {
        echo "Berhasil Input Data\n";
    } else {
        echo "Gagal Input Data\n";
    }
} else {
    $gip    = file_get_contents($username . '.ig');
    $gip    = json_decode($gip);
    $cekuki = instagram(1, $gip->useragent, 'feed/timeline/', $gip->cookies);
    $cekuki = json_decode($cekuki[1]);
    if ($cekuki->status != "ok") {
        if ($opsiLogin == '1') {
            $ulang = masuk($username);
        } else {
            $ulang = masuk2($username, $password);
        }
        if ($ulang != "data berhasil diinput") {
            echo "Cookie Telah Mati, Gagal Membuat Ulang Cookie\n";
        } else {
            echo "Cookie Telah Mati, Sukses Membuat Ulang Cookie\n";
        }
    } else {
        echo "Type? (1 = Followers)\n Input : ";
        $type = trim(fgets(STDIN));
        if ($type == 1) {
            $type = "followers";
        } else {
            $type = "following";
        }
        echo "Kamu Memilih type $type\n";
        echo "Target? (Tanpa @)\nInput : ";
        $target = trim(fgets(STDIN));
        echo "Ketikkan mau komen apa? misal : Follback dong...Jika Ingin Random Pisahkan dengan " | "\nInput : ";
        $komen = trim(fgets(STDIN));
        $komen = explode("|", $komen);
        echo "Ketikkan mau DM apa? misal : Follback dong...Jika Ingin Random Pisahkan dengan " | "\nInput : ";
        $directPesan = trim(fgets(STDIN));
        $directPesan = explode("|", $directPesan);
        $data        = file_get_contents($username . '.ig');
        $data        = json_decode($data);
        
        $userid = instagram(1, $data->useragent, 'users/' . $target . '/usernameinfo', $data->cookies);
        $userid = json_decode($userid[1]);
        $userid = $userid->user->pk;
        if ($type == "followers") {
            $getFoll = instagram(1, $data->useragent, 'friendships/' . $userid . '/followers/', $data->cookies);
        } else {
            $getFoll = instagram(1, $data->useragent, 'friendships/' . $userid . '/following/', $data->cookies);
        }
        $cekfollDecode = json_decode($getFoll[1]);
        $cekfoll = array_slice($cekfollDecode->users, 0);
        $countMaxId = count($cekfollDecode->users);
        $i = 1;
        while($cekfollDecode->next_max_id !== NULL) {
            if($countMaxId === $i){
                if ($type == "followers") {
                    $getFoll = instagram(1, $data->useragent, 'friendships/' . $userid . '/followers?max_id='.$cekfollDecode->next_max_id, $data->cookies);
                } else {
                    $getFoll = instagram(1, $data->useragent, 'friendships/' . $userid . '/following?max_id'.$cekfollDecode->next_max_id, $data->cookies);
                }
                $cekfollDecode = json_decode($getFoll[1]);
                $cekfoll = array_slice($cekfollDecode->users, 0);
                $countMaxId = count($cekfollDecode->users);
                $i = 1;
            }
            $getdata = instagram(1, $data->useragent, 'feed/user/' . $cekfoll->pk . '/', $data->cookies);
            $get     = json_decode($getdata[1]);
            print_r($get);
            $dielz   = $get->items[0]->id;
            if (!file_exists('jedafft-' . $username)) {
                fopen("jedafft-" . $username, "w");
                $no = 0;
            } else {
                $no = file_get_contents('jedafft-' . $username);
            }
            if ($get->message == "Not authorized to view user") {
                echo "User Private Ga Difollow Kentot!1!1! @" . $cekfoll->username . "\n";
            } else {
                $checkFriendship = instagram(1, $data->useragent, 'friendships/show/' . $cekfoll->pk . "/", $data->cookies, generateSignature('{"user_id":"' . $cekfoll->pk . '"}'));
                $checkFriendship = json_decode($checkFriendship[1]);
                if ($checkFriendship->status <> "fail") {
                    if ($checkFriendship->following == true && $checkFriendship->followed_by == true) {
                        echo "Lo Berdua Udah Saling Follow @" . $cekfoll->username . "\n";
                        sleep(2);
                    } elseif ($checkFriendship->following == true && $checkFriendship->followed_by == false) {
                        echo "Lo udah Follow dia, Tapi Dia Belum Follback lo haha sad @" . $cekfoll->username . "\n";
                        sleep(2);
                    } elseif ($checkFriendship->following == false && $checkFriendship->followed_by == true) {
                        echo "Follback Lah dia kentot  @" . $cekfoll->username . "\n";
                        //$follow = instagram(1, $data->useragent, 'friendships/create/' . $cekfoll->pk . "/", $data->cookies, generateSignature('{"user_id":"' . $cekfoll->pk . '"}')); // UNTUK FOLLBACK
                        sleep(2);
                    } else {
                        $follow = instagram(1, $data->useragent, 'friendships/create/' . $cekfoll->pk . "/", $data->cookies, generateSignature('{"user_id":"' . $cekfoll->pk . '"}'));
                        if ($followStatus->status != "ok") {
                            echo "Anda Tidak bisa menggunakan Follow karena Dibanned Sementara atau Permanent, Silahkan Matikan Auto FFT Silahkan Tunggu sampai sudah di unbanned Mrax\n";
                            break;
                        } else {
                            if (count($directPesan) == 1) {
                                $dm = directMessage($cekfoll->pk, $data->useragent, $data->cookies, $directPesan[0]);
                            } else {
                                $dm = directMessage($cekfoll->pk, $data->useragent, $data->cookies, $directPesan[rand(0, count($directPesan) - 1)]);
                            }
                            if (count($komen) == 1) {
                                $comment = instagram(1, $data->useragent, 'media/' . $dielz . '/comment/', $data->cookies, generateSignature('{"comment_text":"' . $komen[0] . '"}'));
                            } else {
                                $comment = instagram(1, $data->useragent, 'media/' . $dielz . '/comment/', $data->cookies, generateSignature('{"comment_text":"' . $komen[rand(0, count($komen) - 1)] . '"}'));
                            }
                            $like          = instagram(1, $data->useragent, 'media/' . $dielz . '/like/', $data->cookies, generateSignature('{"media_id":"' . $dielz . '"}'));
                            $commentStatus = json_decode($comment[1]);
                            $likeStatus    = json_decode($like[1]);
                            $followStatus  = json_decode($followStatus[1]);
                            if ($dm == 'fail' || $commentStatus->status != "ok" || $likeStatus->status != "ok") {
                                echo "DM STATUS => ".$dm.", COMMENT STATUS => ".$commentStatus->status.", LIKE STATUS => ".$likeStatus->status; 
                                sleep(rand(10, 15)); // UBAH SESUAI KEBUTUHAN / PAKE USLEEP()
                            }else{
                                echo "Success Follow @" . $cekfoll->username . " Dengan Auto DM, Like dan Komen Foto Terbaru\n";
                                $h = fopen("jedafft-" . $username, "w");
                                fwrite($h, $no++ . "\n");
                                fclose($h);
                                sleep(rand(10, 15)); // UBAH SESUAI KEBUTUHAN / PAKE USLEEP()
                            }
                        }
                    }  
                } else {
                    echo "Fail Follow @" . $cekfoll->username . " (" . $checkFriendship->message . ")\n";
                    $h = fopen("jedafft-" . $username, "w");
                    fwrite($h, 0);
                    fclose($h);
                }
            }
            $i++;
        }
    } 
}
?> 
