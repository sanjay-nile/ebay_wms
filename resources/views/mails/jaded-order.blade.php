<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
   </head>
   <body>
   	<center>
   	<div style="width:100%;background:#F7F6FA;padding:10px; margin: 0;">
        <div style="width:650px;margin:0 auto;background:#ffffff;">
        	<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, Helvetica, sans-serif;padding:10px;" width="100%">
				<tr>
				 <td>
				    <table width="100%" border="0" align="center"  cellpadding="0" cellspacing="0" style="padding: 10px;">
				       <tr>
				          <td align="center">
				             <a href="#">
				                <img src="{{ asset('images/jaded_logo.jpg') }}" border="0" alt="" style="display: block; height:60px;"/>
				             </a>
				          </td>
				       </tr>
				    </table>
				 </td>
				</tr>
				<tr>
				 <td>
				    <table width="100%" border="0" align="center"  cellpadding="0" cellspacing="0" style="padding: 10px;">
				       <tr>
				          <td>
				             <table border="0" align="center" width="100%" cellpadding="0" cellspacing="0">
				                <!-- <tr>
				                   <td align="center" >
				                      <a href="#">
				                      <img style="display:block; height:275px" src="{{ asset('images/curated-banner.jpg') }}" border="0" alt="" />
				                      </a>
				                   </td>
				                </tr> -->
				                <tr>
				                   <td height="20">&nbsp;</td>
				                </tr>
				                <tr>
				                   <td align="center" style="color: #000000; font-size: 20px;  font-weight:800; line-height: 35px;" >
				                         Hi {{ $user['name'] }},<br> your return has been registered
				                   </td>
				                </tr>
				                <tr>
				                   <td height="30">&nbsp;</td>
				                </tr>                           
				                <tr>
				                   <td align="center">
				                      <table border="0" width="100%" align="center" width="100%" cellpadding="0" cellspacing="0" >
				                         <tr>
				                            <td align="center" style="padding: 10px;">
				                               <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
				                                  <tr>
				                                     <td style="width:47%;color: #000000; vertical-align: top; font-size: 14px; line-height: 20px;">
				                                           <b>Order Number:</b> {{ $user['order_no'] }}
				                                     </td>
				                                     <td style="width:5%"></td>
				                                     <td style="width:47%;color: #000000;vertical-align: top; font-size: 14px;line-height: 20px;">
				                                        <b>Return Service:</b> {{ $user['return_service'] }}
				                                     </td>
				                                  </tr>
				                                  <tr>
				                                     <td height="5">&nbsp;</td>
				                                  </tr>
				                                  <tr>
				                                     <td style="width:47%;color: #000000;vertical-align: top; font-size: 14px;  line-height: 20px;">
				                                          <b>Return Date:</b> {{ $user['return_date'] }}
				                                     </td>
				                                     <td style="width:5%"></td>
				                                     <td style="width:47%;color: #000000;vertical-align: top; font-size: 14px; line-height: 20px;">
				                                        <b>Tracking Number:</b> {{ $user['track_id'] }}
				                                     </td>
				                                  </tr>
				                                  <tr>
				                                     <td height="5">&nbsp;</td>
				                                  </tr>
				                                  <tr>
				                                     <td style="width:47%;color: #000000;vertical-align: top; font-size: 14px; line-height: 20px;">
				                                        {{-- <b>Payment Amount:</b> EUR {{ $user['return_cost'] }} --}}
				                                        <b>Payment Amount: </b>{{ $user['return_charges'] }} {{$user['currency']}} deducted from refund
				                                     </td>
				                                  </tr>
				                               </table>
				                            </td>
				                         </tr>
				                      </table>
				                   </td>
				                </tr>
				             </table>
				          </td>
				       </tr>
				    </table>
				 </td>
				</tr>
			  <tr>
			     <td height="30">&nbsp;</td>
			  </tr> 
			  <tr>
			     <td align="center" style="color: #000000; font-size: 20px;  font-weight:800; line-height: 35px;">How to Return</td>
			  </tr>
			  <tr>
			     <td align="center">
			        <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%">
			           <tr>
			              <td style="width: 33%;padding: 10px; vertical-align: top;" align="center">
			                 <table border="0"  align="center" cellpadding="0" cellspacing="0">
			                    <tr>
			                       <td align="center" style="height: : 33%">
			                          {{-- <a target="_blank" href="{{ $user['url'] }}"> --}}
			                          	<img src="{{ asset('images/printer.png') }}" style="border:none;outline: none; height: 100px" border="0" alt="">
			                          {{-- </a> --}}
			                       </td>
			                    </tr>
			                    
			                    <tr>
			                       <td align="center" style="color: #000000; font-size: 13px;font-weight: 500; line-height: 20px;">
			                       	Print the attached label and secure it to your parcel.
			                       </td>
			                    </tr>
			                 </table>
			              </td>
			              <td style="width: 33%;padding: 10px;  vertical-align: top;" align="center">
			                 <table border="0" align="center" cellpadding="0" cellspacing="0">
			                    <tr>
			                       <td align="center">
			                          <a href="#">
			                          <img src="{{ asset('images/box1.png') }}" style="border:none;outline: none; height: 100px" border="0" alt="">
			                          </a>
			                       </td>
			                    </tr>
			                    
			                    <tr>
								<td align="center" style="color: #000000; font-size: 13px;font-weight: 500; line-height: 20px; ">
                                   Find your local Drop-off location  @if($user['europecountrycheck'] != 1)  <a style="color: #000;" target="_blank" href="{{$user['emailurl']}}"><b><u>here</u></b></a> @endif
			                       </td>
			                    </tr>
			                 </table>
			              </td>
			              <td style="width: 33%; padding: 10px; vertical-align: top;" align="center">
			                 <table border="0" align="center" cellpadding="0" cellspacing="0">
			                    <tr>
			                       <td align="center">
			                          <a href="#">
			                          <img src="{{ asset('images/computer-search1.png') }}" style="border:none;outline: none; height: 100px" border="0" alt="">
			                          </a>
			                       </td>
			                    </tr>
			                    
			                    <tr>
			                       <td align="center" style="color: #000000; font-size: 13px;font-weight: 500; line-height: 20px;">
                                    We’ll keep you updated on your return. Track your return <a style="color: #000;" target="_blank" href="{{url('jaded/tracking')}}"><b><u>here</u></b></a> 
			                       </td>
			                    </tr>
			                 </table>
			              </td>
			           </tr>
			        </table>
			     </td>
			  </tr>
			  
			  <tr>
			     <td height="30">&nbsp;</td>
			  </tr>
			  <tr>
			     <td align="center" style="color: #000000; font-size: 20px; font-weight:800; line-height: 35px;" >
			        <div style="line-height: 35px">
			           Remember to:
			        </div>
			     </td>
			  </tr>
			  <tr>
			     <td height="20">&nbsp;</td>
			  </tr>
			  <tr>
			     <td align="center">
			        <table border="0" align="center" cellpadding="0" cellspacing="0" >
			           <tr>
			              <td align="center" style="font-size: 14px; line-height: 24px; font-weight: 600;">Keep your return receipt until you receive your refund.
			              </td>
			           </tr>
			        </table>
			     </td>
			  </tr>
			  <!-- <tr>
			     <td align="center">
			        <table border="0" align="center" cellpadding="0" cellspacing="0" >
			           <tr>
			              <td align="center" style="font-size: 14px; line-height: 24px; font-weight: 600;">Keep your return receipt until you receive your refund.
			              </td>
			           </tr>
			        </table>
			     </td>
			  </tr>
			  <tr>
			     <td align="center">
			        <table border="0" align="center" cellpadding="0" cellspacing="0" >
			           <tr>
			              <td align="center" style="font-size: 14px; line-height: 24px; font-weight: 600;">*Please kindly note that frequent returns will be monitored
			              </td>
			           </tr>
			        </table>
			     </td>
			  </tr> -->
			  <tr>
			     <td height="15">&nbsp;</td>
			  </tr>
			  {{-- <tr>
			     <td align="center">
			        <table border="0" align="center"  cellpadding="0" cellspacing="0">
			           <tr>
			              <td align="center" style="color: #ffffff; font-size: 12px;line-height: 26px; font-weight: 600;">
			                 <a target="_blank" href="{{ route('missguided.tracking') }}" style="color: #ffffff;text-decoration: none;padding: 10px 15px;display: inline-block;border-radius: 5px;background: #000;">TRACK YOUR RETURN</a>
			              </td>
			           </tr>
			        </table>
			     </td>
			  </tr> --}}
			  <tr>
			     <td height="75">&nbsp;</td>
			  </tr>
			  <tr>
			     <td align="center" style="color: #000000; font-size: 14px;font-weight:700; line-height: 35px;" >
			           Got a question? Visit our  <a target="_blank" href="{{route('jaded.help')}}" style="text-decoration: none;"> <span style="color: #35BD64;">help page</span></a>
			     </td>
			  </tr>
			  <tr>
			     <td height="30" style="border-top: 1px solid #35BD64;">&nbsp;</td>
			  </tr>
			  {{-- <tr>
			     <td>
			        <table border="0"  width="100%" align="center" cellpadding="0" cellspacing="0">
						<tr>
						<td align="center">
						   <a href="#"><img border="0" style="display: block; height: 40px;" src="{{ asset('images/footer-logo.png') }}" alt="" /></a>
						</td>
						</tr>
						<tr>
						    <td height="1">&nbsp;</td>
						</tr>
					</table>
			     </td>
			  </tr> --}}
			</table>
		</div>
	</div>
	</center>
           
   </body>
</html>

