<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class M_analysis_sample extends CI_Model
{
    public $now;
    public $rm;
    public $dbbahanbaku;
    public $dbexcelbbreport;
    public function __construct(){
        parent::__construct();
        $this->now = '%'.date('Y-m-d').'%';
        $this->rm = '11003000010';
        $this->dbbahanbaku     = $this->load->database('bhnbaku',TRUE);
        $this->dbexcelbbreport = $this->load->database('sqlserver77',TRUE);
    }

    public function get_data_sampel($option1,$option2,$option3){
        ($option2==1) ? $condition2 = " z.actor_checkout IS NULL " : $condition2 = ' (z.actor_checkout IS NOT NULL OR z.actor_checkout IS NULL)';
        ($option1==1) ? $condition1 = " (z.lengkap = 'TIDAK' AND z.lengkap_nol = 'TIDAK') " : $condition1 = " (z.lengkap = 'TIDAK' OR z.lengkap = 'YA')";
        ($option3!=1) ? $condition3 = " (z.to_be_composed IS NULL OR z.to_be_composed = 1 OR z.to_be_composed = 0)" : $condition3 = " ((z.to_be_composed = 1 AND z.item_placement IS NULL) OR (z.to_be_composed = 0 AND z.item_placement IS NOT NULL))";
        $condition = '  WHERE '.$condition1.' AND '.$condition2.' AND '.$condition3;
        $query = <<<QUERY
                  SELECT * FROM(
                    SELECT DISTINCT z.id,z.no_sampel,z.item,
                           --z.item_label,
                           CASE
                              WHEN moisture IS NOT NULL THEN z.item_label + ' KERING'
                              ELSE z.item_label
                           END item_label,
                           z.printed,z.printed_label,z.to_be_composed,z.item_placement,z.id_vdni,z.tiketmasuk,z.nomerop,
                           MAX(z.sp) sp,
                           MAX(z.sp_nol) sp_nol,
                           CASE 
                                WHEN MAX(z.sp) = 2 THEN 'LENGKAP'
                                WHEN MAX(z.sp) = 1  THEN 'REVIEW'
                                ELSE 'BELUM LENGKAP'
                           END sp_label,
                           CASE 
                                WHEN MAX(z.sp_nol) = 2 THEN 'LENGKAP'
                                WHEN MAX(z.sp_nol) = 1  THEN 'REVIEW'
                                ELSE 'BELUM LENGKAP'
                           END sp_label_nol,
                           MAX(z.non_sp) non_sp,
                           MAX(z.non_sp_nol) non_sp_nol,
                           CASE 
                                WHEN MAX(z.non_sp) = 2 THEN 'LENGKAP'
                                WHEN MAX(z.non_sp) = 1  THEN 'REVIEW'
                                ELSE 'BELUM LENGKAP'
                           END non_sp_label,
                           CASE 
                                WHEN MAX(z.non_sp_nol) = 2 THEN 'LENGKAP'
                                WHEN MAX(z.non_sp_nol) = 1  THEN 'REVIEW'
                                ELSE 'BELUM LENGKAP'
                           END non_sp_label_nol,
                           CASE 
                                WHEN z.actor_checkout IS NOT NULL THEN 1
                                ELSE NULL
                           END actor_checkout,
                           CASE 
                                WHEN MAX(z.sp)=2 AND MAX(z.non_sp)=2 THEN 'YA'
                                ELSE 'TIDAK'
                            END lengkap,
                           CASE 
                                WHEN MAX(z.sp_nol)=2 AND MAX(z.non_sp_nol)=2 THEN 'YA'
                                ELSE 'TIDAK'
                            END lengkap_nol,
                            min(stamp) stamp,
                            coalesce(no_panggil,SUBSTRING(tiketmasuk,8,10)) no_panggil
                    FROM (
                        SELECT x.id,
                               x.no_sampel,
                               x.item,
                               x.item_label,
                               x.printed,
                               x.printed_label,
                               x.to_be_composed,
                               x.item_placement,
                               x.id_vdni,
                               x.tiketmasuk,
                               x.nomerop,
                               CASE 
                                    WHEN x.sp_review IS NOT NULL THEN 2
                                    WHEN x.sp_review IS NULL AND y.sar_count != 0 AND y.rasi_count = y.sar_count AND y.important = 1  THEN 1
                                    ELSE 0
                               END sp,
                               CASE 
                                    WHEN x.sp_review_nol IS NOT NULL THEN 2
                                    WHEN x.sp_review_nol IS NULL AND y.sar_count != 0 AND y.rasi_count = y.sar_count AND y.important = 1  THEN 1
                                    ELSE 0
                               END sp_nol,
                               CASE 
                                    WHEN x.nonsp_review IS NOT NULL THEN 2
                                    WHEN x.nonsp_review IS NULL AND y.sar_count != 0 AND y.rasi_count = y.sar_count AND y.important = 0  THEN 1
                                    ELSE 0
                               END non_sp,
                               CASE 
                                    WHEN x.nonsp_review_nol IS NOT NULL THEN 2
                                    WHEN x.nonsp_review_nol IS NULL AND y.sar_count != 0 AND y.rasi_count = y.sar_count AND y.important = 0  THEN 1
                                    ELSE 0
                               END non_sp_nol,
                               actor_checkout,
                               CASE 
                                  WHEN x.stamp is not null then GETDATE()
                                  ELSE ''
                               END stamp,
                              no_panggil,
                              moisture
                        FROM (
                            SELECT s.id,
                                   'S1#'+s.id no_sampel,
                                   s.composite_sample,
                                   poi.item,
                                   d.term item_label,
                                   s.printed,
                                   s.to_be_composed,
                                   s.item_placement,
                                   s.sampling_start,
                                   CASE 
                                        WHEN s.printed = 1 THEN 'SUDAH'
                                        ELSE 'BELUM'
                                   END printed_label,
                                   sc.sp_review,
                                   s.sp_review sp_review_nol,
                                   sc.nonsp_review,
                                   s.nonsp_review nonsp_review_nol,
                                   vdn.actor_checkout,
                                   vdni.id id_vdni,
                                   vdn.id tiketmasuk, 
                                   doc.external_id + case 
                                      when bl.batch is null then '' 
                                      else '( '+bl.batch+' )' 
                                    end nomerop,
                                  ac.stamp,
                                  vcn.call_number no_panggil,
                                   poi.moisture
                            FROM sample s
                            LEFT JOIN item_placement ip ON ip.id = s.item_placement
                            JOIN vendor_delivery_note_item vdni ON ip.vendor_delivery_note_item = vdni.id
                            LEFT JOIN bill_of_lading bl ON bl.id = vdni.bill_of_lading
                            LEFT JOIN purchase_order_item poi ON poi.id = bl.purchase_order_item OR vdni.purchase_order_item = poi.id
                            inner join purchase_order po on po.id = poi.purchase_order
                            inner join document doc on doc.id = po.document
                            JOIN dictionary d ON d.id = poi.item
                            LEFT JOIN vendor_delivery_note vdn ON vdn.id = vdni.vendor_delivery_note
                            LEFT JOIN vehicle_call_number vcn ON vcn.vendor_delivery_note = vdn.id AND vcn.void = 0
                            left join actor ac on ac.id = s.sampling_start
                            left join sample sc on sc.id = s.composite_sample
                            where s.item_placement IS NOT NULL
                            AND s.sampling_start IS NOT NULL
                            
                            UNION ALL
                            SELECT sc.id,
                                   'S1#'+sc.id no_sampel,
                                   sc.composite_sample,
                                   poi.item,
                                   d.term item_label,
                                   sc.printed,
                                   sc.to_be_composed,
                                   sc.item_placement,
                                   sc.sampling_start,
                                   CASE 
                                        WHEN sc.printed = 1 THEN 'SUDAH'
                                        ELSE 'BELUM'
                                   END printed_label,
                                   sc.sp_review,
                                   s.sp_review sp_review_nol,
                                   sc.nonsp_review,
                                   s.nonsp_review nonsp_review_nol,
                                   vdn.actor_checkout,
                                   null id_vdni,
                                   null tiketmasuk,
                                   null nomerop,
                                   ac.stamp,
                                   vcn.call_number no_panggil,
                                   poi.moisture
                            FROM sample s
                            JOIN item_placement ip ON ip.id = s.item_placement
                            JOIN vendor_delivery_note_item vdni ON ip.vendor_delivery_note_item = vdni.id
                            LEFT JOIN bill_of_lading bl ON bl.id = vdni.bill_of_lading
                            LEFT JOIN purchase_order_item poi ON poi.id = bl.purchase_order_item OR vdni.purchase_order_item = poi.id
                            left join purchase_order po on po.id = poi.purchase_order
                            left join document doc on doc.id = po.document
                            JOIN dictionary d ON d.id = poi.item
                            JOIN vendor_delivery_note vdn ON vdn.id = vdni.vendor_delivery_note
                            LEFT JOIN vehicle_call_number vcn ON vcn.vendor_delivery_note = vdn.id AND vcn.void = 0
                            left join actor ac on ac.id = s.sampling_start
                            join sample sc on sc.id = s.composite_sample
                            where sc.composite_sample IS NULL AND s.sampling_start IS NOT NULL
                            
                            ) X
                        LEFT JOIN (
                            SELECT s.id,COUNT(rasi.id) rasi_count,COUNT(sar.id) sar_count,rasi.important,composite_sample FROM sample s
                            LEFT JOIN rm_acceptance_standard_item rasi ON rasi.rm_acceptance_standard = s.rm_acceptance_standard
                            LEFT JOIN sample_analysis_result sar ON (sar.sample = s.id OR sar.sample = s.composite_sample) AND sar.rm_acceptance_standard_item = rasi.id
                            WHERE composite_sample IS NOT NULL
                            GROUP BY s.id,rasi.important,composite_sample
                            UNION ALL
                            SELECT tmp.id,COUNT(rasi.id) rasi_count,tmp.sar_count,tmp.important,s.composite_sample FROM (
                              SELECT s.id,COUNT(sar.id) sar_count,rasi.important FROM sample s
                              JOIN sample_analysis_result sar ON sar.sample = s.id
                              JOIN rm_acceptance_standard_item rasi ON rasi.id = sar.rm_acceptance_standard_item
                              GROUP BY s.id,rasi.important
                            ) tmp
                            JOIN rm_acceptance_standard_item rasi ON rasi.important = tmp.important
                            JOIN sample s ON s.rm_acceptance_standard = rasi.rm_acceptance_standard AND s.id = tmp.id
                            AND s.composite_sample IS NULL
                            GROUP BY tmp.id,tmp.sar_count,tmp.important,s.composite_sample
                            ) Y
                        ON x.id = y.id
                    ) z
                GROUP BY z.id,z.no_sampel,z.item,z.item_label,z.printed,z.printed_label,z.actor_checkout,z.to_be_composed,z.item_placement,z.id_vdni,z.tiketmasuk,z.nomerop,z.no_panggil,z.moisture
            ) z
            $condition
            ORDER BY z.no_sampel DESC
QUERY;
        #echo $query;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function get_bahan_baku(){
        $query = <<<QUERY
                SELECT id id, term label
                FROM dictionary
                WHERE context = 'RM'
                ORDER BY term ASC
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function get_sample_composit($sample){
    /*	
        $query = <<<QUERY
                SELECT s.id
                FROM sample s JOIN sample sc ON s.composite_sample = sc.id
                WHERE s.composite_sample = :sample
QUERY;
*/
    	$query = <<<QUERY
		SELECT s.id
    			,s.item_placement
    			,doc.external_id + case 
 					when bl.batch is null then '' 
    				else '( '+bl.batch+' )' 
    			end nomerop
                FROM sample s 
				JOIN sample sc ON s.composite_sample = sc.id
				join item_placement ip on ip.id = s.item_placement
				join vendor_delivery_note_item vdni on vdni.id = ip.vendor_delivery_note_item
				left join bill_of_lading bl on bl.id = vdni.bill_of_lading 
				join purchase_order_item poi on poi.id = bl.purchase_order_item or  poi.id = vdni.purchase_order_item
				join purchase_order po on po.id = poi.purchase_order
				join document doc on doc.id = po.document				
                WHERE s.composite_sample = :sample    	
QUERY;
    	
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_active_memo($sample){
        $query = <<<QUERY
                            SELECT COALESCE(ras_khusus.id,ras_biasa.id) ras_id,
                                   COALESCE(ras_khusus.special,ras_biasa.special) special
                            FROM (
                            SELECT ras.id,ras.rm_class,ras.special
                                        FROM rm_acceptance_standard ras
                                        WHERE ras.approver IS NOT NULL
                                            AND ras.period_start <= CURRENT_TIMESTAMP
                                            AND ( ras.period_end IS NULL OR ras.period_end >= CURRENT_TIMESTAMP )
                                            AND ras.special = 0
                                            AND ras.rm_class = (SELECT ras.rm_class FROM rm_acceptance_standard ras
                                                                JOIN sample s ON s.rm_acceptance_standard = ras.id
                                                                WHERE s.id = '$sample'
                                                                )
                            )ras_biasa
                            LEFT JOIN (
                            SELECT ras.id,ras.rm_class,ras.special
                                        FROM rm_acceptance_standard ras
                                        WHERE ras.approver IS NOT NULL
                                            AND ras.period_start <= CURRENT_TIMESTAMP
                                            AND ( ras.period_end IS NULL OR ras.period_end >= CURRENT_TIMESTAMP )
                                            AND ras.special = 1
                                            AND ras.rm_class = (SELECT ras.rm_class FROM rm_acceptance_standard ras
                                                                JOIN sample s ON s.rm_acceptance_standard = ras.id
                                                                WHERE s.id = '$sample'
                                                                )
                            )ras_khusus ON ras_khusus.rm_class = ras_biasa.rm_class
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function printed_sample_children($sample){
        $query = <<<QUERY
                SELECT * FROM sample s
                JOIN sample sc ON sc.id = s.id
                WHERE s.composite_sample = :sample
                AND s.printed = 0
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function check_sample_start($sample){
        $query = <<<QUERY
                SELECT * FROM sample WHERE id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function get_detail_sampel_komposit($sample){
        $query = <<<QUERY
                SELECT * FROM sample WHERE composite_sample = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_sampel($sample_prefix,$composit){
      $sample = explode('#',$sample_prefix);
      $sample1 = $sample[1];
      $composit == 1 ? $condition = " WHERE s.composite_sample = '$sample1' " : $condition = " WHERE s.id = '$sample1' ";
      //$composit == 1 ? $sample_composit = " s.composite_sample " : $sample_composit = " s.id ";
    	$composit != 1 ? $vehicle_segment = " ip.vehicle_segment " : $vehicle_segment = " NULL vehicle_segment ";
      
        $query = <<<QUERY
                SELECT DISTINCT 
                       CASE
                          WHEN poi.moisture IS NOT NULL THEN d.term + ' KERING'
                          ELSE d.term
                       END rm_label
                       ,po.vendor,substring(doc_po.external_id,1,3) tipe, d.id rm,d_rc.term origin,x.id id_jenis,
                       x.term jenis,d2.id id_parameter,d2.term parameter,rasi.id id_rasi,
                       rasi.important,CAST(rasi.low_value AS FLOAT) low_value,CAST(rasi.high_value AS FLOAT) high_value,rasi.verbatim,av.director_decision,
                       sar.id id_sar,CAST(sar.value AS FLOAT) value,sar.verbatim verbatim_sar,d4.term verbatim_label,
                       CASE 
                            WHEN av.director_decision = 'ACCEPT' THEN 'Disetujui'
                            WHEN av.director_decision = 'REJECT' THEN 'Ditolak'
                            ELSE NULL
                       END director_decision_cast, u.id approver, a.stamp,s.sp_review,s.nonsp_review,$vehicle_segment
                FROM sample s
                LEFT JOIN item_placement ip ON ip.id = s.item_placement
                LEFT JOIN vendor_delivery_note_item vdni ON ip.vendor_delivery_note_item = vdni.id
                LEFT JOIN bill_of_lading bl ON bl.id = vdni.bill_of_lading
                LEFT JOIN purchase_order_item poi ON poi.id = bl.purchase_order_item OR vdni.purchase_order_item = poi.id
                LEFT JOIN purchase_order po ON po.id = poi.purchase_order
                LEFT JOIN document doc_po ON po.document = doc_po.id
                LEFT JOIN dictionary d ON d.id = poi.item
                LEFT JOIN rm_acceptance_standard ras ON ras.id = s.rm_acceptance_standard
                LEFT JOIN rm_acceptance_standard_item rasi ON rasi.rm_acceptance_standard = ras.id
                LEFT JOIN rm_analysis_parameter_item rapi ON rapi.id = rasi.rm_analysis_parameter_item
                LEFT JOIN dictionary d2 ON d2.id = rapi.analysis_parameter 
                LEFT JOIN dictionary d3 ON d3.id = LEFT(rapi.analysis_parameter,5)
                LEFT JOIN sample_analysis_result sar ON sar.rm_acceptance_standard_item = rasi.id AND (sar.sample = s.id OR sar.sample = s.composite_sample)
                LEFT JOIN acceptance_verdict av ON av.sample = s.id
                LEFT JOIN actor a ON a.id = av.director
                LEFT JOIN [user] u ON u.id = a.[user]
                LEFT JOIN dictionary d4 ON d4.id = sar.verbatim
                JOIN (
                    SELECT dd1.id id_dd1,dd2.*
                    FROM dictionary dd1
                    JOIN dictionary dd2
                    ON dd1.context = dd2.id
                ) x ON x.id_dd1 = rapi.analysis_parameter
                JOIN rm_class rc ON rc.surrogate = ras.rm_class
                LEFT JOIN dictionary d_rc ON d_rc.id = rc.origin
				        $condition
QUERY;
        #echo $query;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        //$stmt->bindParam(':sample1',$sample[1]);
        //$stmt->bindParam(':sample2',$sample[1]);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_analisa(){
        $query = <<<QUERY
                SELECT d.id AS id_jenis,RIGHT(d.term,5) AS jenis,
                d1.id AS id_parameter,d1.term AS parameter,
                d2.id AS id_keterangan, d2.term AS keterangan
                FROM dictionary d
                JOIN dictionary d1 ON d1.context=d.id
                LEFT JOIN dictionary d2 ON d2.context=d1.id
                WHERE d.context = 'AP'
                ORDER BY d.term ASC
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $analisa = simpleGrouping($result,'id_jenis');
        foreach($analisa as $key=>$value){
            $analisa[$key] = simpleGrouping($value,'jenis');
        }
        foreach($analisa as $key1=>$value1){
            foreach($value1 as $key2=>$value2){
                $analisa[$key1][$key2] = simpleGrouping($value2,'id_parameter');
                
            }
        }
        foreach($analisa as $key1=>$value1){
            foreach($value1 as $key2=>$value2){
                foreach($value2 as $key3=>$value3){
                    $analisa[$key1][$key2][$key3] = simpleGrouping($value3,'parameter');
                }
            }
        }
        return $analisa;
    }

    public function insert_sample_analysis_result($sample,$id_rasi,$value = NULL,$verbatim){
        //($value==0) ? $value = NULL : $value=$value;
        ($value == '') ? $value = NULL : $value = $value;
        empty($verbatim) ? $verbatim = NULL: $verbatim = $verbatim;
        $query = <<<QUERY
                INSERT INTO 
                    sample_analysis_result(
                        [sample],
                        rm_acceptance_standard_item,
                        value,
                        verbatim
                    )
                output inserted.id
                VALUES (
                    :sample,
                    :id_rasi,
                    :value,
                    :verbatim
                )
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->bindParam(':id_rasi',$id_rasi);
        $stmt->bindParam(':value',$value);
        $stmt->bindParam(':verbatim',$verbatim);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function edit_sample_analysis_result($id_sar,$value = NULL,$verbatim){
        //($value==0) ? $value = NULL : $value=$value;
        ($value == '') ? $value = NULL : $value = $value;
        empty($verbatim) ? $verbatim = NULL: $verbatim = $verbatim;
        $query = <<<QUERY
                UPDATE sample_analysis_result 
                SET value    = :value,
                    verbatim = :verbatim
                output inserted.id
                WHERE id = :id_sar
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':id_sar',$id_sar);
        $stmt->bindParam(':value',$value);
        $stmt->bindParam(':verbatim',$verbatim);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function update_verdict_stamp($id_vdn){
        $query = <<<QUERY
                UPDATE vendor_delivery_note 
                SET verdict_stamp = GETDATE()
                output inserted.verdict_stamp
                WHERE id = :id_vdn
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':id_vdn',$id_vdn);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function get_system_recommendation($sample){
        $query = <<<QUERY
                
                SELECT sar.sample,
                       rapi.analysis_parameter id_parameter,
                       dic.term nama_parameter,
                                 sar.value,
                                 rasi.low_value,
                                 rasi.high_value,
                                 sar.verbatim,
                                 rasi.verbatim,
                                 rasi.important,
                       CASE 
                                     WHEN rapi.analysis_parameter = 'MOISTURE' AND poi.moisture IS NOT NULL THEN
                           CASE 
                            WHEN sar.value <= poi.moisture THEN 1
                            WHEN sar.value > poi.moisture THEN 0
                            ELSE -1
                           END 
                         ELSE
                           CASE 
                            WHEN sar.value BETWEEN rasi.low_value AND rasi.high_value THEN 1
                            WHEN sar.value NOT BETWEEN rasi.low_value AND rasi.high_value THEN 0
                            ELSE -1
                           END 
                       END hasil,
                                 CASE 
                                      WHEN sar.verbatim = rasi.verbatim THEN 1
                                      WHEN sar.verbatim != rasi.verbatim THEN 0
                                      ELSE -1
                                 END keterangan
                       , poi.moisture
                          FROM sample_analysis_result sar
                          JOIN rm_acceptance_standard_item rasi ON rasi.id = sar.rm_acceptance_standard_item
                  JOIN rm_analysis_parameter_item rapi on rapi.id = rasi.rm_analysis_parameter_item
                  JOIN dictionary dic on dic.id = rapi.analysis_parameter
                  JOIN sample s on s.id = sar.sample
                          JOIN item_placement ip ON ip.id = s.item_placement
                          JOIN vendor_delivery_note_item vdni ON ip.vendor_delivery_note_item = vdni.id
                          JOIN vendor_delivery_note vdn ON vdn.id = vdni.vendor_delivery_note
                          LEFT JOIN bill_of_lading bl ON bl.id = vdni.bill_of_lading
                          LEFT JOIN purchase_order_item poi ON poi.id = bl.purchase_order_item OR vdni.purchase_order_item = poi.id
                  WHERE sar.sample = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_system_recommendation_old($sample){
        $query = <<<QUERY
                
                SELECT sar.sample,
                       sar.value,
                       rasi.low_value,
                       rasi.high_value,
                       sar.verbatim,
                       rasi.verbatim,
                       rasi.important,
                       CASE 
                            WHEN sar.value BETWEEN rasi.low_value AND rasi.high_value THEN 1
                            WHEN sar.value NOT BETWEEN rasi.low_value AND rasi.high_value THEN 0
                            ELSE -1
                       END hasil,
                       CASE 
                            WHEN sar.verbatim = rasi.verbatim THEN 1
                            WHEN sar.verbatim != rasi.verbatim THEN 0
                            ELSE -1
                       END keterangan
                FROM sample_analysis_result sar
                JOIN rm_acceptance_standard_item rasi ON rasi.id = sar.rm_acceptance_standard_item
                WHERE sar.sample = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    public function insert_acceptance_verdict($sample,$system_recommendation){
        $query = <<<QUERY
                INSERT INTO 
                    acceptance_verdict(
                        [sample],
                        system_recommendation,
                        qc_confirmation,
                        qc,
                        qc_note,
                        director_decision,
                        director,
                        verdict
                    )
                output inserted.system_recommendation
                VALUES (
                    :sample,
                    :system_recommendation,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL
                )
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->bindParam(':system_recommendation',$system_recommendation);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function insert_actor($creator,$action){
        $query = <<<QUERY
                INSERT INTO 
                    actor(
                        [USER],
                        [ACTION],
                        stamp,
                        _command
                    )
                output inserted.id
                VALUES (
                    :creator,
                    (SELECT id FROM transaction_state WHERE label = :action),
                    GETDATE(),
                    NULL
                )
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':creator',$creator);
        $stmt->bindParam(':action',$action);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function update_sample($approver,$sample,$field){
        $query = <<<QUERY
                UPDATE sample SET {$field} = :approver
                output inserted.{$field}
                WHERE id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':approver',$approver);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function ceck_item_placement($vendor_delivery_note_item,$vehicle_segment){
        $query = <<<QUERY
                select * from item_placement where vendor_delivery_note_item = :vendor_delivery_note_item
                and vehicle_segment = :vehicle_segment and verdict is not null
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':vendor_delivery_note_item',$vendor_delivery_note_item);
        $stmt->bindParam(':vehicle_segment',$vehicle_segment);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function update_acceptance_verdict_final($sample,$verdict){
        $query = <<<QUERY
                UPDATE acceptance_verdict
                SET verdict = :verdict
                output inserted.verdict
                WHERE [sample] = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        
        $stmt->bindParam(':verdict',$verdict);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_acceptance_verdict($sample,$verdict){
        $query = <<<QUERY
                UPDATE acceptance_verdict
                SET system_recommendation = :verdict
                output inserted.system_recommendation
                WHERE [sample] = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        
        $stmt->bindParam(':verdict',$verdict);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_item_placement($vendor_delivery_note_item,$vehicle_segment,$verdict){
        $query = <<<QUERY
                UPDATE item_placement
                SET verdict = :verdict
                output inserted.id
                WHERE vendor_delivery_note_item = :vendor_delivery_note_item
                AND vehicle_segment = :vehicle_segment
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':vendor_delivery_note_item',$vendor_delivery_note_item);
        $stmt->bindParam(':vehicle_segment',$vehicle_segment);
        $stmt->bindParam(':verdict',$verdict);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_sample_printed($sample){
        $query = <<<QUERY
                UPDATE sample
                SET printed = 1
                output inserted.id
                WHERE id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_last_entry_sample($sample,$important){
        $field = ($important == 1) ? 'sp_last_completed_entry' : 'nonsp_last_completed_entry';
        $query = <<<QUERY
                UPDATE sample
                SET $field = GETDATE()
                WHERE id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
    }

    public function sample_stamp($sample){
        $query = <<<QUERY
                select a.stamp from sample s
                join actor a on a.id = s.sampling_start
                WHERE s.id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function check_acceptance_verdict($sample){
        $query = <<<QUERY
                select y.id no_sampel,
                       y.av_verdict,
                       y.vehicle_segment,
                       case
                            when x.av_verdict = y.av_verdict then 1
                            else 0
                       end result
                from (
                    select s.id,
                           ip.vendor_delivery_note_item,
                           av.verdict av_verdict
                    from sample s
                    join item_placement ip on ip.id = s.item_placement
                    left join acceptance_verdict av on av.sample = s.id
                    where s.id = :sample
                ) x
                join (
                    select s.id,
                           ip.vendor_delivery_note_item,
                           av.verdict av_verdict,
                           ip.vehicle_segment
                    from sample s
                    join item_placement ip on ip.id = s.item_placement
                    left join acceptance_verdict av on av.sample = s.id
                ) y on x.vendor_delivery_note_item = y.vendor_delivery_note_item
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_data_to_bhnbaku($sample){
        $query = <<<QUERY
                SELECT s.id [no_sampel], 
                       vdni.vendor_delivery_note [tiket_masuk], 
                       vdn.vehicle_registration_number [no_plat], 
                       RTRIM(ras.rm_class) [bahan_baku], 
                       convert(varchar(19),convert(datetime2, a_ss.stamp)) [ambil_sampel],
                       null [nik],
                       1 [jumlah_sampel],
                       a_sys.[user] [user_lab],
                       av.system_recommendation [keputusan_lab],
                       convert(varchar(19),convert(datetime2, a_sys.stamp)) [waktu_lab],
                       a_qc.[user] [user_qc],
                       av.qc_confirmation [keputusan_qc],
                       convert(varchar(19),convert(datetime2, a_qc.stamp)) [waktu_qc],
                       av.qc_note [keterangan_qc],
                       a_dir.[user] [user_dirut],
                       av.director_decision [keputusan_dirut],
                       convert(varchar(19),convert(datetime2, a_dir.stamp)) [waktu_dirut],
                       1 [approve],
                       0 pecah,
                       CASE 
                            WHEN ip.verdict = 'ACCEPT' THEN 'TERIMA'
                            WHEN ip.verdict = 'REJECT' THEN 'TOLAK'
                            ELSE NULL
                       END [keputusan_akhir]
                        
                FROM sample s
                JOIN item_placement ip ON ip.id = s.item_placement
                JOIN vendor_delivery_note_item vdni ON ip.vendor_delivery_note_item = vdni.id
                JOIN vendor_delivery_note vdn ON vdn.id = vdni.vendor_delivery_note
                LEFT JOIN bill_of_lading bl ON bl.id = vdni.bill_of_lading
                LEFT JOIN purchase_order_item poi ON poi.id = bl.purchase_order_item OR vdni.purchase_order_item = poi.id
                JOIN dictionary d ON d.id = poi.item
                JOIN rm_acceptance_standard ras ON ras.id = s.rm_acceptance_standard
                JOIN rm_acceptance_standard_item rasi ON rasi.rm_acceptance_standard = ras.id
                JOIN rm_analysis_parameter_item rapi ON rapi.id = rasi.rm_analysis_parameter_item
                JOIN dictionary d2 ON d2.id = rapi.analysis_parameter 
                JOIN dictionary d3 ON d3.id = LEFT(rapi.analysis_parameter,5)
                LEFT JOIN sample_analysis_result sar 
                ON sar.rm_acceptance_standard_item = rasi.id AND sar.sample = s.id
                LEFT JOIN acceptance_verdict av ON av.sample = s.id
                LEFT JOIN actor a_sys ON a_sys.id = s.sp_review
                LEFT JOIN actor a_qc ON a_qc.id = av.qc
                LEFT JOIN actor a_dir ON a_dir.id = av.director
                LEFT JOIN actor a_ss ON a_ss.id = s.sampling_start
                LEFT JOIN [user] u ON u.id = a_dir.[user]
                LEFT JOIN dictionary d4 ON d4.id = sar.verbatim
                WHERE s.id = :sample
QUERY;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->bindParam(':sample',$sample);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_T_SAMPEL1_HDR($sample,$tiket,$bb,$ambil_sampel,$nik,$jmlsampel,$keputusan,$keterangan,$approve,$updateby,$approvedate,$approveby,$keputusanawal,$pecah){
        if(empty($keterangan)){
            ($keputusan == 'TOLAK') ? $keterangan = 'NO' : $keterangan = 'OK' ;
        }

        $query = <<<QUERY
                INSERT INTO 
                    T_SAMPEL1_HDR(
                        NO_SAMPEL1,
                        NOMERMASUK,
                        KODE_BARANG,
                        TANGGALAMBIL,
                        NIK,
                        JUMLAHSAMPEL,
                        KEPUTUSAN,
                        KETERANGAN,
                        APPROVE,
                        UPDATED_DATE,
                        UPDATED_BY,
                        APPROVE_DATE,
                        APPROVE_BY,
                        KEPUTUSAN_AWAL,
                        PECAH
                    )
                VALUES (
                    '$sample',
                    'TM#$tiket',
                    '$bb',
                    TO_DATE('$ambil_sampel','YYYY-MM-DD HH24:MI:SS'),
                    '$nik',
                    '$jmlsampel',
                    '$keputusan',
                    '$keterangan',
                    '$approve',
                    SYSDATE,
                    '$updateby',
                    TO_DATE('$approvedate','YYYY-MM-DD HH24:MI:SS'),
                    '$approveby',
                    '$keputusanawal',
                    '$pecah'
                )
QUERY;
        //echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        /*$stmt->bindParam(':NO_SAMPEL1',$sample);
        $stmt->bindParam(':NOMERMASUK',$tiket);
        $stmt->bindParam(':KODE_BARANG',$bb);
        $stmt->bindParam(':TANGGALAMBIL',$qcconfirmation);
        $stmt->bindParam(':NIK',$nik);
        $stmt->bindParam(':JUMLAHSAMPEL',$jmlsampel);
        $stmt->bindParam(':KEPUTUSAN',$keputusan);
        $stmt->bindParam(':KETERANGAN',$keterangan);
        $stmt->bindParam(':APPROVE',$approve);
        $stmt->bindParam(':UPDATE_BY',$updateby);
        $stmt->bindParam(':APPROVE_DATE',$approvedate);
        $stmt->bindParam(':APPROVE_BY',$approveby);
        $stmt->bindParam(':KEPUTUSAN_AWAL',$keputusanawal);
        $stmt->bindParam(':PECAH',$pecah);*/
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_NO_SAMPEL1($sample,$sample_str,$no_pol,$tiket,$update_by){
        $query = <<<QUERY
                INSERT INTO 
                    NO_SAMPEL1(
                        NO_SAMPEL1,
                        NO_SAMPEL1STR,
                        NO_POL,
                        NO_MASUK,
                        UPDATED_DATE,
                        UPDATED_BY
                    )
                VALUES (
                    '$sample',
                    '$sample_str',
                    '$no_pol',
                    'TM#$tiket',
                    SYSDATE,
                    '$update_by'
                )
QUERY;
        //echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        /*
        $stmt->bindParam(':NO_SAMPEL1',$sample);
        $stmt->bindParam(':NOMERMASUK',$sample_str);
        $stmt->bindParam(':KODE_BARANG',$no_pol);
        $stmt->bindParam(':TANGGALAMBIL',$tiket);
        $stmt->bindParam(':JUMLAHSAMPEL',$update_by);*/
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_T_KENDARAANMASUK($sample,$tiket){
        $query = <<<QUERY
                UPDATE T_KENDARAANMASUK SET NO_SAMPEL1 = '$sample' WHERE NOMERMASUK = 'TM#$tiket'
QUERY;
        //echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_rm_T_SAMPEL2_HDR($kode_barang,$tiket){
        $query = <<<QUERY
                UPDATE T_SAMPEL2_HDR SET KODE_BARANG = '$kode_barang' WHERE NOSTRUKTIMBANG = (
                  SELECT NOSTRUKTIMBANG FROM TIMBANG WHERE NOMERMASUK = 'TM#$tiket'
                )
QUERY;
        //echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_data_rm($keyRmCode){
        $persen = empty($keyRmCode) ? '' : '%';
        $keyRmCode = $keyRmCode - 1;
        $rmId0 = $this->rm.'0'.$keyRmCode.''.$persen;
        $rmId1 = $this->rm.'1'.$keyRmCode.''.$persen;
        $query = <<<QUERY
                SELECT * FROM M_BARANG 
                WHERE KODE_BARANG LIKE '$rmId0' 
                OR KODE_BARANG LIKE '$rmId1' 
                ORDER BY NAMA ASC
QUERY;
        #echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function check_entry_count($sample,$important){
        $query = <<<QUERY
                SELECT 
                  s.id,
                  CASE
                    WHEN important = 1 THEN s.sp_last_completed_entry
                    ELSE s.nonsp_last_completed_entry
                  END last_entry_time,
                  CASE
                    WHEN entry_count = must_entry_count THEN 1
                    ELSE 0
                  END result
                FROM (
                  SELECT 
                  '$sample' sampel,
                  $important important,
                  (
                  SELECT COUNT(sar.id) FROM sample_analysis_result sar
                  JOIN rm_acceptance_standard_item rasi ON rasi.id = sar.rm_acceptance_standard_item
                  WHERE sar.sample = '$sample'
                  AND rasi.important = $important
                  ) entry_count, (
                  SELECT COUNT(rasi.id) FROM rm_acceptance_standard_item rasi
                  JOIN sample s ON s.rm_acceptance_standard = rasi.rm_acceptance_standard
                  WHERE s.id = '$sample'
                  AND rasi.important = $important
                  ) must_entry_count
                ) tmp
                JOIN sample s ON s.id = tmp.sampel
QUERY;
        #echo $query;
        $stmt  = $this->dbexcelbbreport->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function check_data($table,$field,$field_value){
        $query = <<<QUERY
                SELECT * FROM $table 
                WHERE $field = '$field_value'
QUERY;
        //echo $query;
        $stmt  = $this->dbbahanbaku->conn_id->prepare($query);
        $stmt->execute();
        $count = count($stmt->fetchAll(PDO::FETCH_ASSOC));
        return ($count >= 1) ? '' : '['.$table.']'; 
    }
} #--/ End: Class ]]