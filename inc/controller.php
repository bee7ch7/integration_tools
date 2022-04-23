<?php

class CustomerData extends ConnectionLMBP {


  function getCustomersForCurrentDate($do = null) {

    if ($do !== null) {

        return $this->select(
      "


      select
      c.store_id, 
      c.client_id, 
      c.card_number, 
      c.created_at, 
      'KLM' as loyc_type
      
      from clients c
      left join cards_changes cc on cc.client_id = c.id
      
      where 1=1
      and (c.created_at > CURRENT_DATE or cc.created_at > CURRENT_DATE)
      
      UNION 
      
      select
      b.store_id, 
      b.client_id, 
      b.card_number, 
      b.created_at, 
      'PRO' as loyc_type
      
      from brigades b
      left join cards_changes cc on cc.client_id = b.id
      
      where 1=1
      and (b.created_at > CURRENT_DATE or cc.created_at > CURRENT_DATE)
      

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

}

class LMorderReader extends ConnectionMordor 
{


      function getDarkStoreOrders($do = null) {

    if ($do !== null) {

        return $this->select(
      "


          select
          l.`store`,
          l.`order`,
          concat(LPAD(l.`store`, 3, 0), l.`order`) as order_fr,
          l.delivery_provider,
          l.payment_type,
          l.payment_method,
          l.delivery_type,
          kht.npn,
          l.lmorder_creation,
          #add_send_date,
          l.kn_send_status_c,
          l.kn_send_status_l,
          l.kn_canceled,
          kht.state_time as status_timestamp,
          kht.status_code,
          kht.status as status_description,
          kht.delivery_cost,
          kht.estimate_delivery_date,
          kht.error_description,
            l.`time` as lmorder_created_at
          
          from logistics l
          left join kn_history_ttnc kht on l.`store` = kht.`store` and l.`order` = kht.`order`
          
          where 1=1
          and l.`store`=901
         # and l.`time` >= current_date
		   and l.`time` >= '2022-02-05'
          order by l.store, l.`order`


      ");
    } else {
      return "error in params";
    }
  }

  
  
}



?>
