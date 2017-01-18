<?php

namespace Jpk;

class Walidator
{
    public function __construct($plik)
    {
        $this->plik = $plik;
        $this->file_contents = file_get_contents($this->plik);

        $this->dom = new \DOMDocument();
        $this->dom->loadXML($this->file_contents);
        $this->dx = new \DOMXPath($this->dom);
        $this->dx->registerNamespace("p", 'http://jpk.mf.gov.pl/wzor/2016/03/09/03095/');
    }

    public function sprawdz_poprawnosc()
    {
        return $this->sprawdz_zgodnosc_struktury();
    }

    public function sprawdz_zgodnosc_struktury($schema_file)
    {
        return $this->dom->schemaValidate($schema_file);
    }

    public function liczba_faktur_ctrl()
    {
        return $this->dx->query('//p:FakturaCtrl/p:LiczbaFaktur')->item(0)->nodeValue;
    }

    public function liczba_faktur()
    {
        return $this->dx->query('//p:Faktura')->length;
    }

    public function liczba_wierszy_ctrl()
    {
        return $this->dx->query('//p:FakturaWierszCtrl/p:LiczbaWierszyFaktur')->item(0)->nodeValue;
    }

    public function liczba_wierszy()
    {
        return $this->dx->query('//p:FakturaWiersz')->length;
    }

    public function wartosc_faktur_ctrl()
    {
        return $this->dx->query('//p:FakturaCtrl/p:WartoscFaktur')->item(0)->nodeValue;
    }

    public function wartosc_faktur()
    {
        $faktury_brutto = $this->dx->query('//p:Faktura/p:P_15');
        $suma_brutto = 0;
        foreach ($faktury_brutto as $brutto)
        {
            $suma_brutto += $brutto->nodeValue;
        }

        return $suma_brutto;
    }

    public function wartosc_faktur_netto()
    {
        // kwoty netto sa w roznych polach dla roznych stawek
        $lista_kwot_netto = $this->dx->query(
            '//p:Faktura/p:P_13_1
            | //p:Faktura/p:P_13_2
            | //p:Faktura/p:P_13_3
            | //p:Faktura/p:P_13_4
            | //p:Faktura/p:P_13_5
            | //p:Faktura/p:P_13_6
            | //p:Faktura/p:P_13_7
        ');
        $suma = 0;
        foreach ($lista_kwot_netto as $kwota)
        {
            $suma += $kwota->nodeValue;
        }

        return $suma;
    }

    public function wartosc_wierszy_ctrl()
    {
        return $this->dx->query('//p:FakturaWierszCtrl/p:WartoscWierszyFaktur')->item(0)->nodeValue;
    }

    public function wartosc_wierszy_netto()
    {
        $wiersze_brutto = $this->dx->query('//p:FakturaWiersz/p:P_11');
        $suma_brutto = 0;
        foreach ($wiersze_brutto as $brutto)
        {
            $suma_brutto += $brutto->nodeValue;
        }

        return $suma_brutto;
    }

    // format dat sprawdza xsd
    public function sprawdz_daty()
    {
        $od = $this->dx->query('//p:Naglowek/p:DataOd')->item(0)->nodeValue;
        $do = $this->dx->query('//p:Naglowek/p:DataDo')->item(0)->nodeValue;
        if ($do < $od)
        {
            return false;
        }

        $daty = $this->dx->query('//p:Faktura/p:P_1');
        foreach ($daty as $data)
        {
            if ($data->nodeValue > $do or $data->nodeValue < $od)
            {
                return false;
            }
        }

        return true;
    }
}
