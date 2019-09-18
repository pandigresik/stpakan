--select * from lpb where NO_LPB = '000027/CJ/XI/2016'
--select * from lpb_e where NO_LPB = '000027/CJ/XI/2016'
--select * from lpb_d where NO_LPB = '000027/CJ/XI/2016'
--select * from review_lpb_budidaya where NO_LPB = '000027/CJ/XI/2016'

select * from sinkronisasi where id = 379
delete from detail_sinkronisasi where sinkronisasi = 384

insert into sinkronisasi([id_ref]
      ,[transaksi]
      ,[asal]
      ,[tujuan]
      ,[aksi]
      ,[tgl_sinkron]
      ,[tgl_buat])
select NULL id_ref,'simpan_pp' transaksi, 'CJ' asal, 'FM' tujuan, 'PUSH' aksi, NULL tgl_sinkron, '2016-11-07 15:28:00' tgl_buat

insert into detail_sinkronisasi
select 384 sinkronisasi, 'I' aksi, 'lpb' tabel
, '{"no_lpb" : "' + no_lpb + '"}' kunci
, 0 status_identity from lpb where no_lpb = '000027/CJ/XI/2016'

insert into detail_sinkronisasi
select 384 sinkronisasi, 'I' aksi, 'lpb_d' tabel
,'{"no_lpb" : "'+no_lpb+'","tgl_keb_awal":"'+ convert(varchar,tgl_keb_awal,121)+'"}' kunci 
, 0 status_identity from lpb_d where no_lpb = '000027/CJ/XI/2016'

insert into detail_sinkronisasi
select 384 sinkronisasi, 'I' aksi, 'lpb_e' tabel
, '{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+convert(varchar,tgl_kebutuhan,121)+'"}' kunci 
, 0 status_identity from lpb_e where no_lpb = '000027/CJ/XI/2016'

insert into detail_sinkronisasi
select distinct 384 sinkronisasi, 'I' aksi, 'review_lpb_budidaya' tabel
, '{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'"}' kunci
, 0 status_identity from review_lpb_budidaya where no_lpb = '000027/CJ/XI/2016'


select * from review_lpb_budidaya where no_lpb = '000027/CJ/XI/2016'