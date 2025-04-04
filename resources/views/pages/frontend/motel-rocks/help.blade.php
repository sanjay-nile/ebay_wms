<div class="help-wrapper">
      <div class="row">
        @include('pages.frontend.motel-rocks.sidebar')
        <div class="col-md-9">
           <div class="rg-step-content">
              <span class="bg-shape"></span>
              <div class="faq-content-container">
                <h3>Our top FAQs</h3>
                <div class="faq-content-body">
                   <div class="faq-info-box-content">
                      <div id="accordion" class="accordion">
                         <div class="card mb-0">
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-1">
                               <a class="card-title">How do I book a return?</a>
                            </div>
                            <div id="collapse-1" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     To organise a return, simply follow the steps in our booking process. Then print your shipping documents, enclose the returns form within the package, fix the label to the outside of your package and send the item back through your chosen method.
                                  </p>
                                  <p>If you are returning from Jersey / Guernsey, please be aware that a printer is required for the return and you will need to select "United Kingdom" when you enter your address.</p>
                                  <p>
                                     If you have chosen the paperless shipping method, please generate the barcode and take it to your closest drop off point, they will then print the label for you and attach this to your parcel.
                                  </p>
                                  <!-- <p>
                                     If you have chosen a drop off service, take your return to the most convenient drop point. If you have chosen a home collection, then the courier will arrive at your chosen pick-up address on the date and time requested.
                                  </p> -->
                                  <p>
                                     You can then track the status of your return until it is delivered to the retailers return warehouse.
                                  </p>
                                  <p>
                                     After this point, the retailer will process your return and arrange for your refund in accordance with their specific returns policy. View <a href="https://motelrocks.zendesk.com/hc/en-us/sections/200516221-Returns-Information" target="_blank" rel="noopener">return help page</a> for more information.</a>.
                                  </p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-2">
                               <a class="card-title">I'm having problems booking my return</a>
                            </div>
                            <div id="collapse-2" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     If you are having trouble registering a return, <a href="https://www.motelrocks.com/pages/contact-us" target="_blank" rel="noopener"><b>contact us</b></a> with your order number and screenshots of the error message you are seeing.
                                  </p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-8">
                               <a class="card-title">My label didn’t download</a>
                            </div>
                            <div id="collapse-8" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">                                  
                                  <p>
                                     After you <a href="{{ route('motel-rocks.home') }}" target="_blank" rel="noopener">create a return
                                     </a> you will be issued with a return label to download and print. If you are unable to download your return label at this stage, please follow the below instructions.
                                  </p>

                                  <p>
                                     To download a return label that you have already generated, go to <a href="{{ route('motel-rocks.tracking') }}" target="_blank" rel="noopener">Track My Return</a> and enter your order details. Before downloading your label, check your browser settings to make sure that ‘download PDFs’ is set to ON.
                                  </p>

                                  <p>
                                     If you are using Google Chrome you can update your browser settings by following these instructions:
                                  </p>
                                  <p>
                                     Step 1: Launch the Chrome browser > click on the three vertical dots on the upper right of the browser > click on Settings in the context menu.
                                  </p>
                                  <p>
                                     Step 2: In the Settings window, click on the Privacy and security option on the left side of the window > on the right-hand side of the pane, click on the arrow next to the Site Settings option.
                                  </p>
                                  <p>
                                     Step 3: In the next window, Go to Additional content settings, scroll down and find PDF documents > click on the arrow next to it.
                                  </p>
                                  <p>
                                    Step 4: In the PDF Documents page, move the slider to turn on the option – Download PDF files instead of automatically opening them in Chrome.
                                  </p>
                                  <p>Now, exit the browser, restart Chrome, and your PDF files should download.</p> 
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-3">
                               <a class="card-title">How much do returns cost?</a>
                            </div>
                            <div id="collapse-3" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     Your return cost will be indicated in your return summary before you click 'submit'. This might be charged as a deduction from your refund (taken off the refund amount owed to you).
                                  </p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-4">
                               <a class="card-title">Can I return multiple order numbers together?</a>
                            </div>
                            <div id="collapse-4" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                 <!--  <p>
                                     Unfortunately we are not able to process multiple orders returned in one parcel.
                                  </p>
                                  <p>
                                     Please book a separate return for each order.
                                  </p> -->
                                  <p>Each order number must have a unique return label for the refund to be processed correctly.</p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-5">
                               <a class="card-title">I no longer want to return my order, how do I cancel?</a>
                            </div>
                            <div id="collapse-5" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     To cancel your return, go to <a href="{{ route('motel-rocks.tracking') }}" target="_blank">track my return </a> and find your order using your tracking number or order number and email address. Scroll to the relevant return and select 'cancel'.
                                  </p>
                                  <p>
                                     Returns can only be cancelled before they are dropped off with the carrier. Once you have dropped your return off, it can no longer be cancelled.
                                  </p>
                                  <p>
                                     You can arrange additional returns for other items from your order by following the  
                                     <a href="{{ route('motel-rocks.home') }}" target="_blank">create a return</a> process.
                                  </p>
                                  <p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-6">
                               <a class="card-title">How do I track my return?</a>
                            </div>
                            <div id="collapse-6" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     To track your return, go to <a href="{{ route('motel-rocks.tracking') }}" target="_blank">track my return </a> and enter your tracking ID (this can be found on your return confirmation email).
                                  </p>
                                  <p>
                                     To view the status of all of your returns, enter your order number and email address.
                                  </p>
                               </div>
                            </div>
                            <div class="card-header collapsed" data-toggle="collapse" href="#collapse-7">
                               <a class="card-title">Why doesn't my return have any tracking updates?</a>
                            </div>
                            <div id="collapse-7" class="card-body collapse" data-parent="#accordion" >
                               <div class="faq-accordion-body">
                                  <p>
                                     If your return doesn't have any tracking updates within 24 hours of delivering it to your chosen drop off point, please <a target="_blank" href="https://www.motelrocks.com/pages/contact-us">contact us</a> providing your order number / tracking number.
                                  </p>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>
                   <div class="">
                      <h4></h4>
                   </div>
                </div>
             </div>
      </div>
        </div>
      </div>
</div>