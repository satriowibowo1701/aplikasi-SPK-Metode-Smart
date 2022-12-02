<?php


function GetMinMax($id, PDO  $db)
{
    $stmt4 = $db->prepare("select nilai_alternatif_kriteria from smart_alternatif_kriteria where id_kriteria = '$id' ORDER BY nilai_alternatif_kriteria DESC");
    $stmt4->execute();
    $datas = $stmt4->fetchAll();
    if (count($datas) > 0) {
        $min = null;
        $max = null;
        foreach ($datas as $data) {
            $min = $min == null ? $data["nilai_alternatif_kriteria"] : ($min < $data["nilai_alternatif_kriteria"] ? $min : $data["nilai_alternatif_kriteria"]);
            $max = $max == null ? $data["nilai_alternatif_kriteria"] : ($max > $data["nilai_alternatif_kriteria"] ? $max : $data["nilai_alternatif_kriteria"]);
        }
        return [$min, $max];
    } else {
        return  false;
    }
}

function GetNameKriteria(PDO $db, $id)
{
    $stmt5 = $db->prepare("select nama_kriteria from smart_kriteria  where id_kriteria = '$id' ");
    $stmt5->execute();
    $p = $stmt5->fetch();
    return $p["nama_kriteria"];
}
