<?php

class CustomerData extends ConnectionLMBP {


  function getCustomersForCurrentDate($do = null) {

    if ($do !== null) {

        return $this->select(
      "


      select
      store_id, client_id, card_number, created_at, 'PRO' as loyc_type
      from brigades b
      where created_at > current_date
      union
      select
      store_id, client_id, card_number, created_at, 'KLM' as loyc_type
      from clients c
      where created_at > current_date


      ;
      ");
    } else {
      return "error in params";
    }
  }



  function Template($do = null) {

    if ($do !== null) {

      $binds = array(
      'store' => $store,
      'order' => $order
    );

        return $this->select(
      "
      select
      `store`,
      `order`,
      `name`,
      `surname`

      from quick_purchase_names
      where 1=1

      and store = :store
      and `order` = :order

      ;
      ", $binds);
    } else {
      return "error in params";
    }
  }



  function getCustomers($limit = null, $offset = null) {

  if ($limit !== null and $offset !== null) {
        $binds = array(
      'limit' => $limit,
      'offset' => $offset

    );

    return $this->select(
  "
  select
  store_id,
  client_id,
  client_name,
  sex,
  card_number,
  address,
  postal_code,
  phone,
  email,
  communication_email,
  communication_sms,
  communication_phone,
  communication_fax,
  created_at,
  updated_at

  from clients c
  where 1=1

  -- and communication_email = 1
  and email is not NULL
  and email <> ''
  and created_at between CURRENT_DATE() - INTERVAL 1 day and CURRENT_DATE()

  limit :limit offset :offset

  ;", $binds);

  } else {
    echo "limit \ offset is not valid";
  }
  }

    function getQtyCustomers() {

    return $this->select(
  "

  select
  count(1) as qty

  from clients c
  where 1=1

  -- and communication_email = 1
  and email is not NULL
  and email <> ''
  and created_at between CURRENT_DATE() - INTERVAL 1 day and CURRENT_DATE()
  ");


  }



}


class OrderTypes extends ConnectionFront {

  function getOrders($limit = null, $offset = null) {

    if ($limit !== null and $offset !== null) {
          $binds = array(
        'limit' => $limit,
        'offset' => $offset

      );

        return $this->select(
      "
      select

    		trim(LEADING '0' FROM substr(o.pyxis_order_uid,1,3)) as store_id,
    		o.pyxis_order_uid,
    		substring(o.pyxis_order_uid from 4) as pyxis_order,

        case
        when o.status in ('PRE_PAYED','PRE_CONFIRMED') then 'INITIALIZED'
        when o.status in ('DELIVERY_IN_PROGRESS','CONFIRMED') then 'IN_PROGRESS'
        when o.status in ('PAYED') then 'DELIVERED'
        when o.status in ('CANCELLED_IN_PYXIS','CANCELLED_BY_COLLECTION_TIMEOUT','CANCELLED_BY_ADMIN') then 'CANCELLED'
        else null
        end as sputnik_status,

    		o.status,
    		o.date,
        substring(cast(o.date as varchar) from 1 for 10) || 'T' || substring(cast(o.date as varchar) from 12 for 8) as date_formated,
    		o.customer_number,
    		o.payment_type,
    		o.payment_method,
    		o.origin,
    		regexp_replace(o.customer_phone, '[^0-9]+', '','g') as customer_phone,
    		d.type,
    		d.provider,

          case
        		when d.type = 'PICKUP_POINT' and d.provider = 'NOVA_POSHTA' then 'Самовивіз із відділеннь ''Нова Пошта'''
        		when d.type = 'COURIER_ADDRESS' and d.provider = 'NOVA_POSHTA' then 'Адресна доставка ''Нова Пошта'''
        		when d.type = 'COURIER_ADDRESS' and d.provider = 'AVITEK_INVEST' then 'Адресна доставка кур''єром'
        		when d.type = 'STORE' and d.provider = 'LEROY_MERLIN' then 'Самовивіз із магазину'
        		when d.type = 'PICKUP_POINT' and d.provider = 'UKR_POSHTA' then 'Самовивіз із відділеннь ''Укрпошта'''
        		when d.type = 'COURIER_ADDRESS' and d.provider = 'UKR_POSHTA' then 'Адресна доставка ''Укрпошта'''
        		end
        		as delivery_description,
          
			    case
        		when d.type = 'PICKUP_POINT' and d.provider = 'NOVA_POSHTA' then concat(d.pickup_point_city,', ',d.pickup_point_address)
        		when d.type = 'COURIER_ADDRESS' and d.provider in ('NOVA_POSHTA','AVITEK_INVEST') then concat(d.recipient_city,', ',d.recipient_street_name,', ',d.recipient_street_number,', ',d.recipient_apartment)
        		when d.type = 'STORE' and d.provider = 'LEROY_MERLIN' then d.recipient_city
        		end
        		as delivery_address_final,


        d.recipient_city,
        d.recipient_street_name,
        d.recipient_street_number,
        d.recipient_apartment,
        d.pickup_point_city,
        d.pickup_point_address,
        d.price



                from orders o
                left join delivery d on o.id = d.order_id

                where 1=1

    		and o.date between NOW() - INTERVAL '1 day' and NOW()
        and o.pyxis_order_uid <> '0000000'
        and o.status in (
                  			'PRE_PAYED',
                  			'PRE_CONFIRMED',
                  			'DELIVERY_IN_PROGRESS',
                  			'CONFIRMED',

                  			'PAYED',
                  			'CANCELLED_IN_PYXIS',
                  			'CANCELLED_BY_COLLECTION_TIMEOUT',
                  			'CANCELLED_BY_ADMIN'
                  		)

    		order by 1, o.date

        limit :limit offset :offset

      ;", $binds);

    } else {
      return "error in params";
    }
  }


  function getQtyOrders() {

  return $this->select(
"

select
count(1) as qty
          from orders o
          left join delivery d on o.id = d.order_id

          where 1=1

  and o.date between NOW() - INTERVAL '1 day' and NOW()
  and o.pyxis_order_uid <> '0000000'
  and o.status in (
                  'PRE_PAYED',
                  'PRE_CONFIRMED',
                  'DELIVERY_IN_PROGRESS',
                  'CONFIRMED',

                  'PAYED',
                  'CANCELLED_IN_PYXIS',
                  'CANCELLED_BY_COLLECTION_TIMEOUT',
                  'CANCELLED_BY_ADMIN'
                )

");


}



}



class PyxisOrderDetails extends ConnectionPyxis {

  function getOrderProducts($store = null, $order = null) {

    if ($store !== null and $order !== null) {

      $binds = array(
      'store' => $store,
      'order' => $order
    );

        return $this->select(
      "
      select

          tl.trl_mag_id as store,
          tl.trl_tra_id,
          t.tra_mnt_total,

          p.pro_codeinterne as lm,
          p.pro_designation as description,
          nmc.nmc_designation,
          substr(nmc.nmc_serial,2,2) as dept,

          case
          when p.pro_uvt_id = '0' then 'тонна'
          when p.pro_uvt_id = '1' then 'літр'
          when p.pro_uvt_id = '3' then 'метр'
          when p.pro_uvt_id = '4' then 'м2'
          when p.pro_uvt_id = '5' then 'м3'
          when p.pro_uvt_id = '6' then 'кг'
          when p.pro_uvt_id = '8' then 'грамм'
          when p.pro_uvt_id = '9' then 'од'
          when p.pro_uvt_id = '92' then 'депо'
          when p.pro_uvt_id = '93' then 'од/1000'
          when p.pro_uvt_id = '94' then 'уп'
          when p.pro_uvt_id = '95' then 'шт'
          when p.pro_uvt_id = '96' then 'ящик'
          when p.pro_uvt_id = '97' then 'палета'
          when p.pro_uvt_id = '98' then 'слой'
          when p.pro_uvt_id = '99' then 'корзина'
          end as unit,
          tl.trl_eta_id as strike,
          tl.trl_prixventebasettc as system_price,
          tl.trl_quantite as quantity,
          tl.trl_pourcentageremise as discount,
          tl.trl_prixclientttc as price_with_discount,
          tl.trl_montantlignettc as total_line


          from
          public.transaction_ligne tl
          left join public.transaction t on tl.trl_tra_id = t.tra_id
          left join public.produit p on p.pro_id = tl.trl_pro_id
          left join nomenclature_niveau nmc on p.pro_nmc_id = nmc.nmc_id

          where
          tl.trl_tra_id = :order
          AND
          tl.trl_mag_id = :store

          --AND tl.trl_tlt_id <> 17
          and tl.trl_eta_id not in (90,220)


      ", $binds);
    } else {
      return "dates or store is not valid";
    }
  }


  function getOrderTotalForCanceledOrder($store = null, $order = null) {

    if ($store !== null and $order !== null) {

      $binds = array(
      'store' => $store,
      'order' => $order
    );

        return $this->select(
      "
      select
          sum(tl.trl_quantite * trl_prixclientttc) as order_total
      from transaction_ligne tl
          where
          tl.trl_tra_id = :order
          AND
          tl.trl_mag_id = :store


      ", $binds);
    } else {
      return "dates or store is not valid";
    }
  }



}

?>
