<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Pengaturan;
use App\Siswa;
use App\Kelas;
use App\Jurusan;
use App\Semester;
use App\Ekskul;
use App\Pkl;
use App\Prestasi;
use App\NilaiAkhir;
use App\NilaiSikap;
use App\Absensi;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\IOFactory;
use \Carbon\Carbon;

class CetakController extends Controller
{
    public function index()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas();
        $pass['tempat_tanggal'] = Pengaturan::vget('kabupaten') . ", " . $this->indonesian_month(date("j n Y"));
        return view('cetak.panel', $pass);
    }

    public function make(Request $request)
    {
        $docObj = new PhpWord();

        $schDetails = Pengaturan::get_school_details();
        $printLocDate = $request->input('tempat_tanggal');
        $headmaster = Pengaturan::get_headmaster();
        $semester = Semester::get_active_semester();

        $siswa_to_print = [];
        $nis_array = [];
        if($request->input('siswa') == 'kelas') {
            $siswa_to_print = Siswa::where('id_kelas', $request->input('kelas'))->get();
        }
        elseif($request->input('siswa') == 'nis') {
            $nis_array = explode(' ', $request->input('nis'));
            foreach($nis_array as $nis) {
                if(count($range = explode('-', $nis)) == 2) {
                    $range = Siswa::whereBetween('nis', $range)->get();
                    foreach($range as $get) {
                        $siswa_to_print[] = $get;
                    }
                } elseif($get = Siswa::where('nis', $nis)->first()) {
                    $siswa_to_print[] = $get;
                } else {
                    return back()->with('message', "NIS $nis tidak ditemukan.");
                }
            }
        }
        else {
            return back();
        }

        if(count($siswa_to_print) < 1) {
            return back()->with('message', "Tidak ada data untuk dicetak.");
        }

        $id_kelas = 0;
        $mapel_list = null;
        foreach($siswa_to_print as $siswa) {
            if($siswa->id_kelas == null) { continue; }

            if(!Semester::is_active_latest()) {
                $siswa->kelas_link->tingkat -= Semester::get_year_difference();
            }

            if($request->input('cover')) {
                $docObj = $this->wordHalamanCover($docObj, $siswa);
                $docObj = $this->wordHalamanDataSekolah($docObj, $schDetails);
            }
            if($request->input('bio')) {
                $docObj = $this->wordHalamanBiodata($docObj, $siswa, $printLocDate, $headmaster);
            }
            if($request->input('nilai')) {
                if($id_kelas != $siswa->id_kelas) {
                    $id_kelas = $siswa->id_kelas;
                    $mapel_list = NilaiAkhir::get_mapel_list($id_kelas);
                }

                $sikap = NilaiSikap::get_nilai($siswa->id);
                $nilai['sikap'] = $sikap ? $sikap->sikap : "";
                $nilai['mapel'] = NilaiAkhir::get_all_nilai($siswa, $mapel_list);
                $absensi = Absensi::get_absensi($siswa->id);
                $nilai['absensi'] = [
                    'sakit' => $absensi ? $absensi->sakit : null,
                    'izin' => $absensi ? $absensi->izin : null,
                    'alpa' => $absensi ? $absensi->alpa : null
                ];
                $nilai['prestasi'] = Prestasi::get_for_print($siswa->id, $semester->id);
                $nilai['ekskul'] = Ekskul::get_for_print($siswa->id, $semester->id);
                $nilai['pkl'] = Pkl::get_for_print($siswa->id, $semester->id);

                $docObj = $this->wordHalamanNilai($docObj, $schDetails, $siswa, $nilai, $headmaster, $printLocDate, $semester);
            }
        }

        $objWriter = IOFactory::createWriter($docObj, 'Word2007');
        $fn = date('Y-n-j_His');
        if($request->input('siswa') == 'kelas') {
            $kelas = Kelas::find($request->input('kelas'));
            if(!Semester::is_active_latest()) { $kelas->tingkat -= Semester::get_year_difference(); }
            $fn .= "_" . $kelas->name(false);
        }
        if($request->input('siswa') == 'nis') {
            $fn .= "_" . $nis_array[0] . '-' . $nis_array[count($nis_array)-1];
        }
        $objWriter->save("print/{$fn}.docx");

        return Response::download("print/{$fn}.docx", "{$fn}.docx", ['Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    public function wordHalamanCover($docObj, $subject)
    {
        $section = $docObj->addSection();
        $section->getStyle()->setMarginTop(Converter::cmToTwip(3));
        $section->getStyle()->setMarginBottom(Converter::cmToTwip(5));

        $fStyle = array('name' => 'Times New Roman', 'size' => 18, 'bold' => true, 'allCaps' => true);
        $pStyle = array('align' => 'center', 'spaceAfter' => 0, 'spaceBefore' => 0);
        $section->addText('Rapor Siswa', $fStyle, $pStyle);
        $section->addText('Sekolah Menengah Kejuruan', $fStyle, $pStyle);
        $section->addText('(SMK)', $fStyle, $pStyle);

        $section->addTextBreak(3);

        $section->addImage(
            '../resources/assets/images/tutwurihandayani.jpg',
            array(
                'width' => Converter::cmToPixel(4),
                'height' => Converter::cmToPixel(4),
                'marginTop' => 0,
                'marginLeft' => 0,
                'align' => 'center',
                'wrappingStyle' => 'inline',
            )
        );

        $section->addTextBreak(2);

        $section->addText('Nama Peserta Didik:', array('name' => 'Times New Roman', 'size' => 14, 'bold' => true), array('align' => 'center', 'spaceAfter' => 240));

        $name = $section->addTable(array('borderColor' => '000000', 'borderSize' => 1, 'align' => 'center', 'width' => 100));
        $name->addRow(Converter::cmToPixel(1.5));
        $nameCell = $name->addCell(Converter::cmToTwip(14.5));
        $nameCell->addText($subject->nama, array('name' => 'Times New Roman', 'size' => '16', 'bold' => true, 'allCaps' => true), array('align' => 'center', 'spaceBefore' => 120, 'spaceAfter' => 120));

        $section->addTextBreak(2);

        $section->addText('NISN:', array('name' => 'Times New Roman', 'size' => 14, 'bold' => true), array('align' => 'center', 'spaceAfter' => 240));

        $name = $section->addTable(array('borderColor' => '000000', 'borderSize' => 1, 'align' => 'center', 'width' => 100));
        $name->addRow(Converter::cmToPixel(1.5));
        $nameCell = $name->addCell(Converter::cmToTwip(14.5));
        $nameCell->addText($subject->nisn, array('name' => 'Times New Roman', 'size' => '16', 'bold' => true, 'allCaps' => true), array('align' => 'center', 'spaceBefore' => 120, 'spaceAfter' => 120));

        $section->addTextBreak(3);

        $fStyle = array('name' => 'Times New Roman', 'size' => 16, 'bold' => true, 'allCaps' => true);
        $pStyle = array('align' => 'center', 'spaceAfter' => 0, 'spaceBefore' => 0);
        $section->addText('Kementerian Pendidikan dan Kebudayaan', $fStyle, $pStyle);
        $section->addText('Republik Indonesia', $fStyle, $pStyle);

        return $docObj;
    }

    public function wordHalamanDataSekolah($docObj, $schDetails)
    {
        $section = $docObj->addSection();
        $section->getStyle()->setMarginTop(Converter::cmToTwip(2.5));
        $section->getStyle()->setMarginBottom(Converter::cmToTwip(2));
        $section->getStyle()->setMarginLeft(Converter::cmToTwip(2.5));
        $section->getStyle()->setMarginRight(Converter::cmToTwip(2.5));

        $fStyle = array('name' => 'Times New Roman', 'size' => 12, 'bold' => true, 'allCaps' => true);
        $pStyle = array('align' => 'center', 'spaceAfter' => 240, 'spaceBefore' => 0, 'lineHeight' => 1.0);
        $section->addText('Rapor Siswa', $fStyle, $pStyle);
        $section->addText('Sekolah Menengah Kejuruan', $fStyle, $pStyle);
        $section->addText('(SMK)', $fStyle, $pStyle);

        $section->addTextBreak(1);

        $docObj->addParagraphStyle(
            'Detail Sekolah',
            array(
                'align' => 'left',
                'spaceAfter' => 240,
                'spaceBefore' => 0,
                'indent' => Converter::cmToTwip(1 / 720),
                'lineHeight' => 1.0,
                'tabs' => array(
                    new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(5)),
                    new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(5.5))
                ),
            )
        );

        $fStyle = array('name' => 'Times New Roman', 'size' => 12, 'bold' => false, 'allCaps' => false);
        $nameFStyle = array('name' => 'Times New Roman', 'size' => 12, 'bold' => false, 'allCaps' => true);

        $schName = $section->addTextRun('Detail Sekolah');
        $schName->addText("Nama Sekolah\t:\t", $fStyle);
        $schName->addText(htmlspecialchars($schDetails['nama_sekolah']), $nameFStyle);
        $section->addText("NPSN\t:\t".$schDetails['npsn'], $fStyle, 'Detail Sekolah');
        $section->addText("NIS/NSS/NDS\t:\t".$schDetails['nss'], $fStyle, 'Detail Sekolah');
        $alamat = explode("\n", $schDetails['alamat_sekolah']);
        for($i = 0; $i < count($alamat); $i++) {
            if($i == 0) {
                $section->addText(htmlspecialchars("Alamat Sekolah\t:\t".$alamat[0]), $fStyle, 'Detail Sekolah');
            } else {
                $section->addText(htmlspecialchars("\t\t".$alamat[$i]), $fStyle, 'Detail Sekolah');
            }
        }
        $section->addText(htmlspecialchars("Kelurahan\t:\t".$schDetails['kelurahan']), $fStyle, 'Detail Sekolah');
        $section->addText(htmlspecialchars("Kecamatan\t:\t".$schDetails['kecamatan']), $fStyle, 'Detail Sekolah');
        $section->addText(htmlspecialchars("Kabupaten/Kota\t:\t".$schDetails['kabupaten']), $fStyle, 'Detail Sekolah');
        $section->addText(htmlspecialchars("Provinsi\t:\t".$schDetails['provinsi']), $fStyle, 'Detail Sekolah');
        $section->addText(htmlspecialchars("Website\t:\t".$schDetails['website']), $fStyle, 'Detail Sekolah');
        $section->addText(htmlspecialchars("Email\t:\t".$schDetails['email']), $fStyle, 'Detail Sekolah');

        return $docObj;
    }

    public function wordHalamanBiodata($docObj, $siswa, $printLocDate, $headmaster)
    {
        $section = $docObj->addSection();
        $section->getStyle()->setMarginTop(Converter::cmToTwip(2));
        $section->getStyle()->setMarginBottom(Converter::cmToTwip(1.5));
        $section->getStyle()->setMarginLeft(Converter::cmToTwip(3));
        $section->getStyle()->setMarginRight(Converter::cmToTwip(3));

        $fStyle = array('name' => 'Times New Roman', 'size' => 11, 'bold' => true, 'allCaps' => true);
        $pStyle = array('align' => 'center', 'spaceAfter' => 240, 'spaceBefore' => 0);

        $section->addText('KETERANGAN TENTANG DIRI SISWA', $fStyle, $pStyle);

        $fStyle = array('name' => 'Times New Roman', 'size' => 11);
        $numberingPStyle = array('indent' => Converter::cmToTwip(7.25 / 720), 'hanging' => Converter::cmToTwip(7.25 / 720), 'spaceAfter' => 120, 'lineHeight' => 1.0, 'tabs' => array(
            new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(0.8)),
            new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(7)),
        ));

        $section->addText("1.\tNama Peserta Didik (lengkap)\t: ".strtoupper($siswa->nama), $fStyle, $numberingPStyle);
        $section->addText("2.\tNIS / NISN\t: ".$siswa->nis.' / '.$siswa->nisn, $fStyle, $numberingPStyle);
        $section->addText("3.\tTempat, Tanggal Lahir\t: ".$siswa->tempat_lahir.', '.$this->indonesian_month(Carbon::parse($siswa->tanggal_lahir)->format('j n Y')), $fStyle, $numberingPStyle);
        $section->addText("4.\tJenis Kelamin\t: ".($siswa->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan'), $fStyle, $numberingPStyle);
        $section->addText("5.\tAgama\t: ".$siswa->agama, $fStyle, $numberingPStyle);
        $section->addText("6.\tStatus dalam Keluarga\t: Anak ".$siswa->status_dalam_keluarga, $fStyle, $numberingPStyle);
        $section->addText("7.\tAnak ke\t: ".$siswa->anak_ke, $fStyle, $numberingPStyle);
        $textlines = explode("\n", $siswa->alamat_siswa);
        for($i = 0; $i < count($textlines); $i++) {
            if($i == 0) {
                $section->addText("8.\tAlamat Peserta Didik\t: ".$textlines[0], $fStyle, $numberingPStyle);
            } else {
                $section->addText("\t\t  ".$textlines[$i], $fStyle, $numberingPStyle);
            }
        }
        $section->addText("\tNomor Telepon Rumah\t: ".$siswa->nomor_telepon_rumah_siswa, $fStyle, $numberingPStyle);
        $section->addText("9.\tSekolah Asal\t: ".strtoupper($siswa->sekolah_asal), $fStyle, $numberingPStyle);
        $section->addText("10.\tDiterima di sekolah ini", $fStyle, $numberingPStyle);
        $section->addText("\ta. Di kelas\t: ".strtoupper($siswa->diterima_di_kelas), $fStyle, $numberingPStyle);
        $section->addText("\tb. Pada tanggal\t: ".$this->indonesian_month(Carbon::parse($siswa->tanggal_diterima)->format('j n Y')), $fStyle, $numberingPStyle);
        $section->addText("11.\tNama Orang Tua", $fStyle, $numberingPStyle);
        $section->addText("\ta. Ayah\t: ".strtoupper($siswa->nama_ayah), $fStyle, $numberingPStyle);
        $section->addText("\tb. Ibu\t: ".strtoupper($siswa->nama_ibu), $fStyle, $numberingPStyle);
        $textlines = explode("\n", $siswa->alamat_orang_tua);
        for($i = 0; $i < count($textlines); $i++) {
            if($i == 0) {
                $section->addText("12.\tAlamat Orang Tua\t: ".$textlines[0], $fStyle, $numberingPStyle);
            } else {
                $section->addText("\t\t  ".$textlines[$i], $fStyle, $numberingPStyle);
            }
        }
        $section->addText("\tNomor Telepon Rumah\t: ".$siswa->nomor_telepon_rumahorang_tua, $fStyle, $numberingPStyle);
        $section->addText("13.\tPekerjaan Orang Tua", $fStyle, $numberingPStyle);
        $section->addText("\ta. Ayah\t: ".$siswa->pekerjaan_ayah, $fStyle, $numberingPStyle);
        $section->addText("\tb. Ibu\t: ".$siswa->pekerjaan_ibu, $fStyle, $numberingPStyle);
        $section->addText("14.\tNama Wali Peserta Didik\t: ".$siswa->nama_wali, $fStyle, $numberingPStyle);
        $textlines = explode("\n", $siswa->alamat_wali);
        for($i = 0; $i < count($textlines); $i++) {
            if($i == 0) {
                $section->addText("15.\tAlamat Wali Peserta Didik\t: ".$textlines[0], $fStyle, $numberingPStyle);
            } else {
                $section->addText("\t\t  ".$textlines[$i], $fStyle, $numberingPStyle);
            }
        }
        $section->addText("\tNomor Telepon Rumah\t: ".$siswa->nomor_telepon_rumah_wali, $fStyle, $numberingPStyle);
        $section->addText("16.\tPekerjaan Wali Peserta Didik\t: ".$siswa->pekerjaan_wali, $fStyle, $numberingPStyle);

        $table = $section->addTable(array('borderSize' => 0, 'borderColor' => 'ffffff', 'align' => 'left', 'indent' => Converter::cmToTwip(2 / 720)));
        $table->addRow(Converter::cmToTwip(4.2));
        $table->addCell(Converter::cmToTwip(2), array('width' => Converter::cmToTwip(2)));
        $cell = $table->addCell(Converter::cmToTwip(3.2));
        $cell->addTextBreak(1);
        $imgTable = $cell->addTable(array('borderSize' => 2, 'borderColor' => '000000'));
        $imgTable->addRow(Converter::cmToTwip(4), array('exactHeight' => true));
        $cell = $imgTable->addCell(Converter::cmToTwip(3), array('width' => Converter::cmToTwip(3)));
        if(file_exists(base_path('resources/assets/images/pasfotosiswa/'.$siswa->id.'.jpg'))) {
            $cell->addImage(
                base_path('/resources/assets/images/pasfotosiswa/'.$siswa->id.'.jpg'),
                array(
                    'width' => Converter::cmToPixel(3),
                    'height' => Converter::cmToPixel(4),
                    'marginTop' => 0,
                    'marginLeft' => 0,
                    'align' => 'center',
                    'wrappingStyle' => 'inline',
                )
            );
        } else {
            $cell->addText('Pas Foto', $fStyle, array('align' => 'center'));
            $cell->addText('3 x 4', $fStyle, array('align' => 'center'));
        }
        $table->addCell(Converter::cmToTwip(3), array('width' => Converter::cmToTwip(3)));
        $cell = $table->addCell(Converter::cmToTwip(6.5));
        $signaturePStyle = array('spaceAfter' => 0, 'spaceBefore' => 120);
        $cell->addText($printLocDate, $fStyle, $signaturePStyle);
        $cell->addText('Kepala Sekolah,', $fStyle, $signaturePStyle);
        $cell->addTextBreak(3);
        $cell->addText($headmaster['kepala_sekolah.nama'], $fStyle, array_merge($signaturePStyle, array('borderBottomColor' => '000000', 'borderBottomSize' => 1)));
        $cell->addText('NIP '.$headmaster['kepala_sekolah.nip'], $fStyle, $signaturePStyle);

        return $docObj;
    }

    public function wordHalamanNilai($docObj, $sekolah, $siswa, $dataRapor, $headmaster, $printLocDate, $semester)
    {
        $section = $docObj->addSection(['orientation' => 'landscape']);

        $fStyle = array('name' => 'Times New Roman', 'size' => 11);
        $boldFStyle = array_merge($fStyle,['bold'=>true]);

        $pStyle = ['align' => 'left', 'spaceAfter' => 60, 'spaceBefore' => 0];

        $docObj->addParagraphStyle(
            'Bio Nilai',
            array(
                'align' => 'left',
                'spaceAfter' => 60,
                'spaceBefore' => 0,
                'indent' => Converter::cmToTwip(0),
                'lineHeight' => 1.0,
                'tabs' => array(
                    new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(4)),
                    new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(16)),
                    new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(20))
                ),
            )
        );

        $nilaiTitlePStyle = [
            'align' => 'left',
            'spaceAfter' => 60,
            'spaceBefore' => 0,
            'indent' => Converter::cmToTwip(0),
            'lineHeight' => 1.0,
            'tabs' => [
                new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(1))
            ],
            'keepNext' => true
        ];

        $section->addText("Nama Sekolah\t: ".$sekolah['nama_sekolah']."\tKelas\t: ".$siswa->kelas_link->name(false), $fStyle, 'Bio Nilai');
        $alamat = str_limit(explode("\n", $sekolah['alamat_sekolah'])[0], 32, '');
        $semText = $semester->semester;
        if($semText == 1) { $semText .= " (Satu)"; }
        if($semText == 2) { $semText .= " (Dua)"; }
        $section->addText("Alamat\t: ".$alamat."\tSemester\t: ".$semText, $fStyle, 'Bio Nilai');
        $section->addText("Nama Siswa\t: ".$siswa->nama."\tTahun Pelajaran\t: ".$semester->tahun_ajaran, $fStyle, 'Bio Nilai');
        $section->addText("Nomor Induk/NISN\t: ".$siswa->nisn, $fStyle, 'Bio Nilai');

        $section->addTextBreak(1);

        $section->addText("CAPAIAN HASIL BELAJAR", $boldFStyle, $pStyle);
        $section->addText("A.\tSikap", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.5)]);

        $table->addRow(Converter::cmToTwip(8), ['exactHeight' => Converter::cmToTwip(8)]);
        $cell = $table->addCell(Converter::cmToTwip(22.5), ['width' => Converter::cmToTwip(22.5)]);
        $cell->addText('Deskripsi:', $fStyle, $pStyle);
        $cell->addText($dataRapor['sikap'], $fStyle, $pStyle);

        $section->addPageBreak();

        $section->addText("B.\tPengetahuan dan Keterampilan", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.1), 'width' => 100]);

        $cellRowSpan = array('vMerge' => 'restart');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 4);
        $cellHeader = array('bgColor' => 'FDE8D8', 'valign' => 'center');
        $cellVCentered = array('valign' => 'center');
        $cellHCentered = array('align' => 'center', 'spaceBefore' => 30, 'spaceAfter' => 30);
        $cellHCenteredNoSpacing = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);
        $cellXCentered = array_merge($cellVCentered, $cellHCentered);
        $headerPStyle = array_merge($cellHCenteredNoSpacing, ['keepNext' => true]);

        $table->addRow(0, ['tblHeader' => true]);
        $cell = $table->addCell(Converter::cmToTwip(1), array_merge($cellRowSpan, $cellHeader));
        $cell->addText("No", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(4.5), array_merge($cellRowSpan, $cellHeader));
        $cell->addText("Mata Pelajaran", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(8), array_merge($cellColSpan, $cellHeader));
        $cell->addText("Pengetahuan", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(8), array_merge($cellColSpan, $cellHeader));
        $cell->addText("Keterampilan", $boldFStyle, $headerPStyle);

        $table->addRow(0, ['tblHeader' => true]);
        $cell = $table->addCell(Converter::cmToTwip(1), array_merge($cellRowContinue, $cellHeader));
        $cell = $table->addCell(Converter::cmToTwip(4.5), array_merge($cellRowContinue, $cellHeader));
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("KB", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(1.5), $cellHeader);
        $cell->addText("Angka", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(2), $cellHeader);
        $cell->addText("Predikat", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(5), $cellHeader);
        $cell->addText("Deskripsi", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("KB", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(1.5), $cellHeader);
        $cell->addText("Angka", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(2), $cellHeader);
        $cell->addText("Predikat", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(5), $cellHeader);
        $cell->addText("Deskripsi", $boldFStyle, $headerPStyle);

        foreach($dataRapor['mapel'] as $kelompok => $mapels) {
            $namakelompok = $kelompok;
            if($kelompok == "C1" || $kelompok == "C2" || $kelompok == "C3") {
                $namakelompok = "C";
            }
            $no = 1;
            $table->addRow();
            $cell = $table->addCell(Converter::cmToTwip(1), ['gridSpan' => 10, 'valign' => 'center', 'align' => 'left']);
            $cell->addText('Kelompok '.$namakelompok, $fStyle, ['spaceAfter' => 0]);

            foreach($mapels as $mapel => $nilai) {
                $table->addRow();
                $cell = $table->addCell(Converter::cmToTwip(1));
                $cell->addText($no++, $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(4.5));
                $cell->addText($mapel, $fStyle, ['spaceAfter' => 0]);
                $cell = $table->addCell(Converter::cmToTwip(1));
                $cell->addText($nilai['pengetahuan']['kb'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(1.5));
                $cell->addText($nilai['pengetahuan']['angka'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(2));
                $cell->addText($nilai['pengetahuan']['predikat'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(5));
                $cell->addText($nilai['pengetahuan']['deskripsi'], $fStyle, ['spaceAfter' => 0]);
                $cell = $table->addCell(Converter::cmToTwip(1));
                $cell->addText($nilai['keterampilan']['kb'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(1.5));
                $cell->addText($nilai['keterampilan']['angka'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(2));
                $cell->addText($nilai['keterampilan']['predikat'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
                $cell = $table->addCell(Converter::cmToTwip(5));
                $cell->addText($nilai['keterampilan']['deskripsi'], $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
            }
        }

        $section->addTextBreak(1);

        $section->addText("C.\tPraktik Kerja Lapangan", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.1), 'width' => 100]);

        $table->addRow(0, ['tblHeader' => true]);
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("No", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(8),$cellHeader);
        $cell->addText("Mitra DU/DI", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(5), $cellHeader);
        $cell->addText("Lokasi", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("Lamanya (bulan)", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(9), $cellHeader);
        $cell->addText("Keterangan", $boldFStyle, $headerPStyle);

        if(empty($dataRapor['pkl'])) {
            $dataRapor['pkl'] = [
                0 => [
                    'mitra' => '',
                    'lokasi' => '',
                    'lama' => '',
                    'keterangan' => ''
                ]
            ];
        }
        $no = 1;
        foreach($dataRapor['pkl'] as $pkl) {
            $table->addRow();
            $cell = $table->addCell(Converter::cmToTwip(1));
            $cell->addText($no++, $fStyle, array_merge(['spaceAfter' => 0], $cellHCentered));
            $cell = $table->addCell(Converter::cmToTwip(8));
            $cell->addText($pkl['mitra'], $fStyle, ['spaceAfter' => 0]);
            $cell = $table->addCell(Converter::cmToTwip(5));
            $cell->addText($pkl['lokasi'], $fStyle, ['spaceAfter' => 0]);
            $cell = $table->addCell(Converter::cmToTwip(1));
            $cell->addText($pkl['lama'], $fStyle, array_merge(['spaceAfter' => 0], $cellHCentered));
            $cell = $table->addCell(Converter::cmToTwip(9));
            $cell->addText($pkl['keterangan'], $fStyle, ['spaceAfter' => 0]);
        }

        $section->addTextBreak(1);

        $section->addText("D.\tEkstra Kurikuler", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.1), 'width' => 100]);

        $table->addRow(0, ['tblHeader' => true]);
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("No", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(8), $cellHeader);
        $cell->addText("Kegiatan Ekstrakurikuler", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(15), $cellHeader);
        $cell->addText("Keterangan", $boldFStyle, $headerPStyle);

        if(empty($dataRapor['ekskul'])) {
            $dataRapor['ekskul'] = [
                0 => [
                    'ekstrakurikuler' => '',
                    'nilai' => ''
                ]
            ];
        }
        $no = 1;
        foreach($dataRapor['ekskul'] as $ekskul) {
            $table->addRow();
            $cell = $table->addCell(Converter::cmToTwip(1));
            $cell->addText($no++, $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
            $cell = $table->addCell(Converter::cmToTwip(8));
            $cell->addText($ekskul['ekstrakurikuler'], $fStyle, ['spaceAfter' => 0]);
            $cell = $table->addCell(Converter::cmToTwip(15));
            $cell->addText($ekskul['nilai'], $fStyle, ['spaceAfter' => 0]);
        }

        // Prestasi
        $section->addTextBreak(1);

        $section->addText("E.\tPrestasi", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.1), 'width' => 100]);

        $table->addRow(0, ['tblHeader' => true]);
        $cell = $table->addCell(Converter::cmToTwip(1), $cellHeader);
        $cell->addText("No", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(8), $cellHeader);
        $cell->addText("Jenis Prestasi", $boldFStyle, $headerPStyle);
        $cell = $table->addCell(Converter::cmToTwip(15), $cellHeader);
        $cell->addText("Keterangan", $boldFStyle, $headerPStyle);

        if(empty($dataRapor['prestasi'])) {
            $dataRapor['prestasi'] = [
                0 => [
                    'prestasi' => '',
                    'keterangan' => ''
                ]
            ];
        }
        $no = 1;
        foreach($dataRapor['prestasi'] as $prestasi) {
            $table->addRow();
            $cell = $table->addCell(Converter::cmToTwip(1));
            $cell->addText($no++, $fStyle, array_merge($cellHCentered, ['spaceAfter' => 0]));
            $cell = $table->addCell(Converter::cmToTwip(8));
            $cell->addText($prestasi['prestasi'], $fStyle, ['spaceAfter' => 0]);
            $cell = $table->addCell(Converter::cmToTwip(15));
            $cell->addText($prestasi['keterangan'], $fStyle, ['spaceAfter' => 0]);
        }

        // Absensi
        $section->addTextBreak(1);

        $section->addText("F.\tKetidakhadiran", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.2), 'indent' => Converter::cmToTwip(1 / 720)]);

        $table->addRow();
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("Sakit", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("    ".$dataRapor['absensi']['sakit'] . "\thari", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);

        $table->addRow();
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("Izin", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("    ".$dataRapor['absensi']['izin'] . "\thari", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);

        $table->addRow();
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("Tanpa Keterangan", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);
        $cell = $table->addCell(Converter::cmToTwip(5));
        $cell->addText("    ".$dataRapor['absensi']['alpa'] . "\thari", $fStyle, ['keepNext' => true, 'spaceAfter' => 0]);

        // Catatan Wali Kelas
        $section->addTextBreak(1);

        $section->addText("G.\tCatatan Wali kelas", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.5)]);

        $table->addRow(Converter::cmToTwip(1.5), ['exactHeight' => Converter::cmToTwip(1.5)]);
        $cell = $table->addCell(Converter::cmToTwip(22.5), ['width' => Converter::cmToTwip(22.5)]);

        // Catatan Wali K
        $section->addTextBreak(1);

        $section->addText("H.\tTanggapan Orang tua/Wali", $boldFStyle, $nilaiTitlePStyle);

        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'align' => 'center', 'cellMargin' => Converter::cmToTwip(0.5)]);

        $table->addRow(Converter::cmToTwip(1.5), ['exactHeight' => Converter::cmToTwip(1.5)]);
        $cell = $table->addCell(Converter::cmToTwip(22.5), ['width' => Converter::cmToTwip(22.5)]);

        // Naik Kelas
        $section->addTextBreak(2);

        if($semester->semester == 2) {
            $section->addText("Keputusan:", $boldFStyle, ['keepNext' => true, 'spaceAfter' => 30]);
            $section->addText("Berdasarkan hasil yang dicapai pada semester 1 dan 2, peserta didik ditetapkan", $fStyle, ['keepNext' => true, 'spaceAfter' => 120]);

            $tab = ['tabs' => [ new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(3)) ]];
            // Kalau naik
            // if() {
                $section->addText("Naik ke kelas\t....................", $fStyle, array_merge($tab, ['keepNext' => true, 'spaceAfter' => 30]));
            // } else {
                $section->addText("Tinggal di kelas\t....................", $fStyle, array_merge($tab, ['keepNext' => true, 'spaceAfter' => 30]));
            // }

            $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
            $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        }

        // Tanda tangan
        $tabs = ['tabs' => [ new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(9)), new \PhpOffice\PhpWord\Style\Tab('left', Converter::cmToTwip(18)) ]];
        $section->addText("Mengetahui:\t\t{$printLocDate}", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText("Orang Tua/Wali,\t\tWali Kelas,", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $wali_kelas = $siswa->kelas_link->get_wali_kelas();
        $wali_kelas_nama = $wali_kelas ? $wali_kelas->nama : "..........................";
        $wali_kelas_nip = $wali_kelas ? $wali_kelas->nip : "";
        $section->addText("..............................\t\t{$wali_kelas_nama}", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText("\t\tNIP. {$wali_kelas_nip}", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText("\tMengetahui,", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText("\tKepala Sekolah,", $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $section->addText(" ", $fStyle, ['keepNext' => true, 'spaceAfter' => 30]);
        $section->addText("\t".$headmaster['kepala_sekolah.nama'], $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));
        $section->addText("\tNIP. ".$headmaster['kepala_sekolah.nip'], $fStyle, array_merge(['keepNext' => true, 'spaceAfter' => 30], $tabs));

        return $docObj;
    }

    public function indonesian_month($d)
    {
        $conv = array(
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli',
            8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        );

        $d = explode(' ', $d);
        $d[1] = $conv[$d[1]];

        return implode(' ', $d);
    }
}
