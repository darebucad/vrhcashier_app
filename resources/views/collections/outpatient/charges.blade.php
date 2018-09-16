

<!-- 

	@foreach ( json_decode($product->tags['data'])->tags as $tag)
    {{$tag->type}} 
@endforeach


@foreach($outpatient_charges as $charge)

	<tr>
		<td>{{ $charge->is_pay }}</td>
		<td>{{ $charge->is_discount }}</td>
		<td>{{ $charge->dodate }}</td>
		<td>{{ $charge->pcchrgcod }}</td>
		<td>{{ $charge->product_description }}</td>
		<td>{{ $charge->qtyissued }}</td>
		<td>{{ $charge->pchrgup }}</td>
		<td>{{ $charge->disc_percent }}</td>
		<td>{{ $charge->disc_amount }}</td>
		<td>{{ $charge->pcchrgamt }}</td>
		<td>{{ $charge->estatus }}</td>

	</tr>

@endforeach -->