<?php

class Faktura_Test extends Jpk_Test
{
    function test_suma_netto()
    {
        $faktura = $this->stworz_fakture();

        $wiersz1 = new \Jpk\Faktura_wiersz(); $wiersz1->nazwa = 'towar1';
        $wiersz1->cenaJednostkowNetto = '100';
        $faktura->dodaj_wiersz($wiersz1);

        $wiersz2 = new \Jpk\Faktura_wiersz();
        $wiersz2->nazwa = 'towar2';
        $wiersz2->cenaJednostkowNetto = '22.8';
        $wiersz2->ilosc = 3;
        $faktura->dodaj_wiersz($wiersz2);

        $this->assertEquals('168.4', $faktura->suma('netto'));
        return $faktura;
    }

    /**
     * @depends test_suma_netto
     */
    function test_suma_brutto($faktura)
    {
        $this->assertEquals('207.13', $faktura->suma('brutto'));
    }

    function test_suma_stawka0()
    {
        $faktura = $this->stworz_fakture();

        $wiersz1 = new \Jpk\Faktura_wiersz();
        $wiersz1->nazwa = 'towar1';
        $wiersz1->cenaJednostkowNetto = '100';
        $wiersz1->stawkaVat = 0;
        $faktura->dodaj_wiersz($wiersz1);

        $this->assertEquals(0, $faktura->suma('podatek'));
        $this->assertEquals(100, $faktura->suma('brutto'));
        $this->assertEquals(100, $faktura->suma('netto'));
    }
}