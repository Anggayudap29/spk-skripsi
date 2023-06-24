<?php

namespace App\Http\Controllers;

use App\Models\Surat_Keputusan;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class SuratKeputusanController extends Controller
{
    public function index()
    {
        $sk_model = new Surat_Keputusan();
        $sk = $sk_model->getAllSK();

        if (count($sk) > 0) {
            $i = 0;
            foreach ($sk as $s) {
                $data[$i] = [
                    "kode_suratkeputusan" => $s->kode_suratkeputusan,
                    "periode" => $s->periode,
                    "nilai_saw" => $s->nilai_saw,
                    "keterangan" => $s->keterangan,
                    "tanggal" =>
                    date("d", strtotime($s->tanggal)) .
                        "-" .
                        getBulan(date("m", strtotime($s->tanggal))) .
                        "-" .
                        date("Y", strtotime($s->tanggal)),
                    "nama" => $s->nama,
                ];
                $i++;
            }
            return $data;
        } else {
            return $sk;
        }
    }

    public function show($periode)
    {
        $sk_model = new Surat_Keputusan();
        $sk = $sk_model->getSKByPeriode($periode);
        return $sk;
    }

    public function cetak_surat_keputusan($kode_hasil)
    {
        date_default_timezone_set("Asia/Jakarta");

        $sk_model = new Surat_Keputusan();
        $sk = $sk_model->getSKByKodeHasil($kode_hasil);

        $pdf = new Fpdf();
        $pdf->AddPage("P", "A4");
        $pdf->Image("images/logo_yia.png", 11, 5, 31, 31);
        $pdf->Cell(25);
        $pdf->SetTextColor(11, 102, 35);
        $pdf->SetFont("Times", "B", "15");
        $pdf->Cell(0, 8, "YAYASAN ISLAM AL-AYANIYAH [YIA]", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetFont("Times", "B", "17");
        $pdf->SetTextColor(255, 199, 00);
        $pdf->Cell(0, 6, "SEKOLAH MENENGAH ATAS ISLAM AL-AYANIYAH", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetTextColor(00, 000, 00);
        $pdf->SetFont("Times", "B", "14");
        $pdf->Cell(0, 7, "Izin Kanwil DEPDIKBUD JABAR NO : 394/1-02/KEP/E-89", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetFont("Times", "B", 9);
        // Removes bold
        $pdf->SetFont("");
        $pdf->Cell(
            0,
            3,
            "Jl. Halim Perdana Kusuma No. 56-60 Kebon Besar Batu Ceper Tangerang 15122 Telp. 021 2225 2531",
            0,
            1,
            "C"
        );
        $pdf->Cell(25);


        $pdf->SetLineWidth(1);

        $pdf->Line(10, 38, 198, 38);
        $pdf->SetLineWidth(0);
        $pdf->Line(10, 39, 198, 39);
        $pdf->Ln(14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(15);
        $pdf->SetFont("Times", "BU", "14");
        $pdf->Cell(0, 5, "SURAT KEPUTUSAN", 0, 1, "C");
        $pdf->Cell(15);
        $pdf->SetFont("Times", "", "12");
        $pdf->Cell(
            0,
            5,
            "SK/" .
                substr($sk->kode_suratkeputusan, 2) .
                "/" .
                date("d", strtotime($sk->tanggal)) .
                "/" .
                getRomawi(date("m", strtotime($sk->tanggal))) .
                "/" .
                date("Y", strtotime($sk->tanggal)),
            0,
            1,
            "C"
        );
        $pdf->Ln(10);

        $pdf->SetFont("Times", "B", "12");
        // $pdf->Cell(30,5,'Kode Saw ',0,0,'L');
        //  $pdf->SetFont('Times','','12');
        // $pdf->Cell(50,5,' : '.$sk->kode_saw,0,1,'L');



        $pdf->SetFont("Times", "B", "12");
        $pdf->Cell(30, 5, "Nama Guru ", 0, 0, "L");
        $pdf->SetFont("Times", "", "12");
        $pdf->Cell(50, 5, " : " . $sk->nama, 0, 1, "L");

        $pdf->SetFont("Times", "B", "12");
        $pdf->Cell(30, 5, "Nilai ", 0, 0, "L");
        $pdf->SetFont("Times", "", "12");
        $pdf->Cell(50, 5, " : " . $sk->nilai_saw, 0, 1, "L");
        $pdf->Ln(7);
        $pdf->Cell(
            70,
            5,
            "           Berdasarkan hasil penilaian yang sudah dilakukan, dan dengan pertimbangan kepala sekolah, maka guru",
            0,
            1,
            "L"
        );
        $pdf->Cell(
            70,
            5,
            "dengan nama di atas dinobatkan sebagai guru terbaik pada periode " .
                $sk->periode .
                " dan berhak mendapatkan penghargaan",
            0,
            1,
            "L"
        );
        $pdf->Cell(
            70,
            5,
            "berupa uang sebesar Rp 1.500.000,00.",
            0,
            1,
            "L"
        );
        $pdf->Ln(2);
        $pdf->Cell(
            50,
            6,
            "Demikian surat keputusan ini dibuat untuk dipergunakan dengan sebagaimana mestinya.",
            0,
            1
        );

        $pdf->Ln(30);
        $pdf->SetFont("Times", "", "12");
        date_default_timezone_set("Asia/Jakarta");
        // $currentdate = date("d-M-Y");
        $pdf->Cell(
            326,
            9,
            "Tangerang, " .
                date("d", strtotime($sk->tanggal)) .
                "-" .
                getBulan(date("m", strtotime($sk->tanggal))) .
                "-" .
                date("Y", strtotime($sk->tanggal)),
            0,
            1,
            "C"
        );
        $pdf->Cell(305, 3, "Mengetahui,", 0, 1, "C");
        $pdf->Cell(310, 5, "Kepala Sekolah", 0, 1, "C");

        $pdf->SetFont("Times", "BU", "12");
        $pdf->Cell(319, 60, "ATO UL AZIS, SE", 0, 1, "C");

        $pdf->Output();
        exit();
    }

    public function destroy($kode)
    {
        DB::table("surat_keputusan")
            ->where("kode_suratkeputusan", $kode)
            ->delete();
        $data = [
            "status" => true,
            "message" => "Data Surat Keputusan berhasil dihapus",
        ];
        return response()->json($data, 200);
    }

    public function cetak_laporan_sk(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "periode_awal" => "required",
            "periode_akhir" => "required",
        ]);

        if ($validation->fails()) {
            return redirect("/surat-keputusan")
                ->withErrors($validation)
                ->withInput();
        }
        $periode_awal = $request->periode_awal;
        $periode_akhir = $request->periode_akhir;

        if ($periode_awal > $periode_akhir) {
            return redirect()->to("/surat-keputusan");
        }

        $sk_model = new Surat_Keputusan();

        $sk = $sk_model->getLaporanSK($periode_awal, $periode_akhir);

        if (count($sk) == 0) {
            return redirect()->to("/surat-keputusan");
        }

        $pdf = new Fpdf();
        $pdf->AddPage("P", "A4");
        $pdf->Image("images/logo_yia.png", 11, 5, 31, 31);
        $pdf->Cell(25);
        $pdf->SetTextColor(11, 102, 35);
        $pdf->SetFont("Times", "B", "15");
        $pdf->Cell(0, 8, "YAYASAN ISLAM AL-AYANIYAH [YIA]", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetFont("Times", "B", "17");
        $pdf->SetTextColor(255, 199, 00);
        $pdf->Cell(0, 6, "SEKOLAH MENENGAH ATAS ISLAM AL-AYANIYAH", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetTextColor(00, 000, 00);
        $pdf->SetFont("Times", "B", "14");
        $pdf->Cell(0, 7, "Izin Kanwil DEPDIKBUD JABAR NO : 394/1-02/KEP/E-89", 0, 1, "C");
        $pdf->Cell(25);
        $pdf->SetFont("Times", "B", 9);
        // Removes bold
        $pdf->SetFont("");
        $pdf->Cell(
            0,
            3,
            "Jl. Halim Perdana Kusuma No. 56-60 Kebon Besar Batu Ceper Tangerang 15122 Telp. 021 2225 2531",
            0,
            1,
            "C"
        );
        $pdf->Cell(25);


        $pdf->SetLineWidth(1);

        $pdf->Line(10, 38, 198, 38);
        $pdf->SetLineWidth(0);
        $pdf->Line(10, 39, 198, 39);
        $pdf->Ln(14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(15);
        $pdf->SetFont("Times", "BU", "14");
        $pdf->Cell(0, 5, "LAPORAN SURAT KEPUTUSAN", 0, 1, "C");
        $pdf->SetFont("Times", "", "12");

        $pdf->Ln(18);

        $pdf->SetFont("Times", "B", "12");
        $pdf->Cell(20, 9, "NO", 1, 0, "C");
        $pdf->Cell(25, 9, "Kode SK", 1, 0, "C");
        $pdf->Cell(35, 9, "Periode Penilaian", 1, 0, "C");
        $pdf->Cell(30, 9, "Tanggal", 1, 0, "C");
        $pdf->Cell(45, 9, "Guru", 1, 0, "C");
        $pdf->Cell(35, 9, "Nilai", 1, 1, "C");
        $no = 1;
        $pdf->SetFont("Times", "", "12");
        foreach ($sk as $s) {
            $pdf->Cell(20, 9, $no++, 1, 0, "C");
            $pdf->Cell(25, 9, $s->kode_suratkeputusan, 1, 0, "C");
            $pdf->Cell(35, 9, $s->periode, 1, 0, "C");
            $pdf->Cell(
                30,
                9,
                date("d", strtotime($s->tanggal)) .
                    "-" .
                    getBulan(date("m", strtotime($s->tanggal))) .
                    "-" .
                    date("Y", strtotime($s->tanggal)),
                1,
                0,
                "C"
            );
            $pdf->Cell(45, 9, $s->nama, 1, 0, "C");
            $pdf->Cell(35, 9, $s->nilai_saw, 1, 1, "C");
        }

        $pdf->Ln(30);
        date_default_timezone_set("Asia/Jakarta");
        // $currentdate = date("d-M-Y");
        $pdf->Cell(
            326,
            9,
            "Tangerang, " .
                date("d") .
                "-" .
                getBulan(date("m")) .
                "-" .
                date("Y"),
            0,
            1,
            "C"
        );
        $pdf->Cell(305, 3, "Mengetahui,", 0, 1, "C");
        $pdf->Cell(310, 5, "Kepala Sekolah", 0, 1, "C");

        $pdf->SetFont("Times", "BU", "12");
        $pdf->Cell(319, 60, "ATO UL AZIS, SE", 0, 1, "C");


        $pdf->Output();
        exit();
    }
}
