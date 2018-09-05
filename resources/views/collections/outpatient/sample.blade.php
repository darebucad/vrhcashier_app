 @foreach($outpatient_charges as $row)
        <tr>
          <td> </td>
          <td> </td>
          <td>{{ $row['dodate'] }}</td>
          <td>{{ $row['pcchrgcod'] }}</td>
          <td>{{ $row['drug_name'] }}</td>
          <td>{{ $row['qtyissued'] }}</td>
          <td>{{ $row['pchrgup'] }}</td>
          <td>{{ $row['disc_percent'] }}</td>
          <td>{{ $row['disc_amount'] }}</td>
          <td>{{ $row['pcchrgamt'] }}</td>
          <td> </td>
          <td> </td>
        </tr>
        @endforeach








         $payment = new Payment;

        $payment->enccode = $request->enccode;
        $payment->hpercode = $request->hpercode;
        $payment->acctno = $request->acctno;
        $payment->orno = $request->or_number;
        $payment->ordate = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $request->ordate)));
        $payment->paycode = $request->payment_mode;
        $payment->paytype = $request->payment_type;
        $payment->curcode = $request->currency;
        $payment->amt = $request->amount_paid;
        $payment->discount_percent = $request->discount_percent;
        $payment->discount_computation = $request->discount_computation;
        $payment->amount_tendered = $request->amount_tendered;
        $payment->change = $request->change;
        $payment->confdl = $request->confdl;
        $payment->payctr = $request->payctr;

        $payment->save();

        return redirect('/collections/outpatient');

        // return redirect()->back();