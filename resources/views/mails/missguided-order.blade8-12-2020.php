<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
   </head>
   <body>
   	<table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background: #F7F6FA">
   		<tr>
   			<td>
   				<table border="0"  cellpadding="0" cellspacing="0" style=" background:#fff;margin:0 auto; max-width: 700px">
					<tr>
						<td>
						   <table border="0" align="center" cellpadding="0" cellspacing="0" >
				               <tr>
				                  <td align="center">
				                     <table border="0" align="center"  cellpadding="0" cellspacing="0" >
				                        <tr>
				                           <td align="center" height="70" >
				                              <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img  border="0" style="display: block; height: 90px;" src="{{ asset('public/images/missguided.png') }}" alt="" height="90px" /></a>
				                           </td>
				                        </tr>
				                     </table>
				                  </td>
				               </tr>
				            </table>
						</td>
					</tr>
					
					<tr>
					<td>
					   <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" >
					      <tr>
					         <td align="center">
					            <table border="0" align="center" width="690" cellpadding="0" cellspacing="0" >
					               <tr>
					                  <td align="center" >
					                     <a href="" style=" border-style: none !important; display: block; border: 0 !important;">
					                     <img src="{{ asset('public/images/lips.png') }}" style="display: block; width: 690px;" width="690" border="0" alt="" />
					                     </a>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center" style="color: #000000; font-size: 24px; font-family: TitlingGothicFB Wide; font-weight:800; line-height: 35px;" >
					                     <div style="line-height: 35px; color: #000;">
					                        Hi {{ $user['name'] }},<br> your return has been registered
					                     </div>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center">
					                     <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" >
					                        <tr>
					                           <td>
					                              <table border="0" width="300" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 14px; font-family: tahoma,arial,helvetica; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px; text-align: left;">
					                                          Order Number: {{ $user['order_no'] }}
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="40" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                                 <tr>
					                                    <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="250" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 14px; font-family: tahoma,arial,helvetica; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px; text-align: left;">
					                                          Return Service: {{ $user['return_service'] }}
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="250" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 16px; font-family: tahoma,arial,helvetica,sans-serif; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px">
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                           </td>
					                        </tr>
					                        <tr>
					                           <td>
					                              <table border="0" width="300" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 14px; font-family: tahoma,arial,helvetica; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px; text-align: left;">
					                                          Return Date: {{ $user['return_date'] }}
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="40" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                                 <tr>
					                                    <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="250" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 13px; font-family: tahoma,arial,helvetica; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px; text-align: left;">
					                                          Tracking Number: {{ $user['track_id'] }}
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                           </td>
					                        </tr>
					                        <tr>
					                           <td>
					                              <table border="0" width="330" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                                 <tr>
					                                    <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                                 </tr>
					                                 <tr>
					                                    <td align="center" style="color: #000000; font-size: 14px; font-family: tahoma,arial,helvetica; font-weight:800; line-height: 20px;">
					                                       <div style="line-height: 20px; text-align: left;">
					                                          Payment Amount: EUR {{ $user['return_cost'] }}
					                                       </div>
					                                    </td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="40" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                                 <tr>
					                                    <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                                 </tr>
					                              </table>
					                              <table border="0" width="40" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                                 <tr>
					                                    <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                                 </tr>
					                              </table>
					                           </td>
					                        </tr>
					                     </table>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="55" style="font-size: 55px; line-height: 55px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center" style="color: #000000; font-size: 22px; font-family: TitlingGothicFB Wide; font-weight:800; line-height: 35px;">
					                     <div style="line-height: 35px; color: #000;">
					                        How to Return
					                     </div>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td>
					                     <table border="0" width="170" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                        <tbody>
					                           <tr>
					                              <td align="center">
					                                 <a href="" style=" border-style: none !important; display: block; border: 0 !important;">
					                                 <img src="{{ asset('public/images/printer.png') }}" style="display: block; width: 130PX;" width="130" border="0" alt="">
					                                 </a>
					                              </td>
					                           </tr>
					                           <tr>
					                              <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                           </tr>
					                           <tr>
					                              <td align="center" style="color: #000000; font-size: 13px; font-family: TitlingGothicFB Wide; font-weight: 600; line-height: 20px; ">
					                                 <div style="line-height: 20px; width: 200px;">
					                                    Print this return label and attach it to your parcel.
					                                 </div>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                     <table border="0" width="30" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                        <tbody>
					                           <tr>
					                              <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                           </tr>
					                        </tbody>
					                     </table>
					                     <table border="0" width="170" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                        <tbody>
					                           <tr>
					                              <td align="center">
					                                 <a href="" style=" border-style: none !important; display: block; border: 0 !important;">
					                                 <img src="{{ asset('public/images/box.png') }}" style="display: block; width: 130PX;" width="130" border="0" alt="">
					                                 </a>
					                              </td>
					                           </tr>
					                           <tr>
					                              <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                           </tr>
					                           <tr>
					                              <td align="center" style="color: #000000; font-size: 13px; font-family: TitlingGothicFB Wide; font-weight: 600; line-height: 20px;">
					                                 <div style="line-height: 20px; width: 200px">
					                                    Drop your return off at your local drop-off point. <a target="_blank" href="https://international.myhermes.co.uk/help-centre/parcels/question/general/where-is-my-nearest-drop-off-point" style="color: #e3b3b7; text-decoration: none;"> Find your local drop-off point</a>
					                                 </div>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                     <table border="0" width="30" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                        <tbody>
					                           <tr>
					                              <td width="40" height="50" style="font-size: 50px; line-height: 50px;"></td>
					                           </tr>
					                        </tbody>
					                     </table>
					                     <table border="0" width="170" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" >
					                        <tbody>
					                           <tr>
					                              <td align="center">
					                                 <a href="" style=" border-style: none !important; display: block; border: 0 !important;">
					                                 <img src="{{ asset('public/images/computer-search.png') }}" style="display: block; width: 130PX;" width="130" border="0" alt="">
					                                 </a>
					                              </td>
					                           </tr>
					                           <tr>
					                              <td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td>
					                           </tr>
					                           <tr>
					                              <td align="center" style="color: #000000; font-size: 13px; font-family: TitlingGothicFB Wide; font-weight: 600; line-height: 20px;">
					                                 <div style="line-height: 20px; width: 200px;">
					                                    We’ll keep you updated on your return. You can also track your return to see where it’s up to.
					                                 </div>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="55" style="font-size: 25px; line-height: 55px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center" style="color: #000000; font-size: 22px; font-family: TitlingGothicFB Wide; font-weight:800; line-height: 35px;" >
					                     <div style="line-height: 35px">
					                        Remember to:
					                     </div>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center">
					                     <table border="0" width="600" align="center" cellpadding="0" cellspacing="0" >
					                        <tr>
					                           <td align="center" style="font-size: 14px; font-family: TitlingGothicFB Wide; line-height: 24px; font-weight: 600;">
					                              <div style="line-height: 24px; width: 590px; color: #000000;">
					                                 Include order paperwork with your return.<br>Keep your return receipt until you receive your refund.
					                              </div>
					                           </td>
					                        </tr>
					                     </table>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center">
					                     <table border="0" align="center" width="300" cellpadding="0" cellspacing="0" bgcolor="#000000" style="">
					                        <tr>
					                           <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
					                        </tr>
					                        <tr>
					                           <td align="center" style="color: #ffffff; font-size: 12px; font-family: TitlingGothicFB Wide; line-height: 26px; font-weight: 600;">
					                              <div style="line-height: 26px;">
					                                 <a target="_blank" href="{{ route('missguided.tracking') }}" style="color: #ffffff; text-decoration: none; ">TRACK YOUR RETURN</a>
					                              </div>
					                           </td>
					                        </tr>
					                        <tr>
					                           <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
					                        </tr>
					                     </table>
					                  </td>
					               </tr>
					               <tr>
					                  <td height="75" style="font-size: 14px; line-height: 25px;">&nbsp;</td>
					               </tr>
					               <tr>
					                  <td align="center" style="color: #000000; font-size: 14px; font-family: TitlingGothicFB Wide; font-weight:700; line-height: 35px;" >
					                     <div style="line-height: 35px">
					                        Got a question? Visit our  <a target="_blank" href="https://www.missguided.eu/help#help-returns-container" style="text-decoration: none;"> <span style="color: #e3b3b7;">help page</span></a>
					                     </div>
					                  </td>
					               </tr>
					            </table>
					         </td>
					      </tr>
					   </table>
					</td>
					</tr>
					<tr>
					<td>
					   <table border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="ffffff" style="width: 690px; margin: auto;">
					      <tr>
					         <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
					      </tr>
					      <tr>
					         <td height="10" style="font-size: 40px; line-height: 10px; ">&nbsp;</td>
					      </tr>
					      <tr>
					         <td height="60" style="border-top: 1px solid #e3b3b7;font-size: 60px; line-height: 60px; ">&nbsp;</td>
					      </tr>
					      <tr>
					         <td align="center">
					            <table border="0" align="center" cellpadding="0" cellspacing="0">
					               <tr>
					                  <td>
					                     <table border="0" width="300" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					                        <tr>
					                           <!-- logo -->
					                           <td align="center">
					                              <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img width="80" border="0" style="display: block; width: 40px;" src="{{ asset('public/images/footer-logo.png') }}" alt="" /></a>
					                           </td>
					                        </tr>
					                     </table>
					                  </td>
					               </tr>
					            </table>
					         </td>
					      </tr>
					      <tr>
					         <td height="60" style="font-size: 60px; line-height: 60px;">&nbsp;</td>
					      </tr>
					   </table>
					</td>
					</tr>
					</table>
   			</td>
   		</tr>
   	</table>
   </body>
</html>