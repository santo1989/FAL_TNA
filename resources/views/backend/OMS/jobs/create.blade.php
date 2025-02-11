<x-backend.layouts.master>
    <style>
        .create_label_column {
            width: 5%;
        }

        .create_input_column {
            width: 10%;
        }

        table {
            font-size: 0.8rem;
        }

        input::placeholder,
        [type="date"] {
            font-size: 0.8rem;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .select2-container--default .select2-selection--single {
            height: 34px;
            width: 100%;
        }
    </style>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <a href="{{ route('home') }}" class="btn btn-md btn-outline-success"><i class="fas fa-arrow-left"></i>
                        Home</a>
                </div>
                <div class="col">
                    <h3 class="text-center p-1">Create Job</h3>
                    @if (session('message'))
                        <div class="alert alert-success">
                            <span class="close" data-dismiss="alert">&times;</span>
                            <strong>{{ session('message') }}.</strong>
                        </div>
                    @endif
                </div>
                <div class="col">
                </div>
            </div>
        </div>

        <x-backend.layouts.elements.errors />
        <div class="row p-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="division_id" value="2">
                            <input type="hidden" name="division_name" value="Factory">
                            <input type="hidden" name="company_id" value="3">
                            <input type="hidden" name="company_name" value="FAL - Factory">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="create_label_column">Job No</td>
                                        <td class="create_input_column">
                                            @php
                                                $job_no = App\Models\Job::max('id') + 1;
                                                $job_no =
                                                    'FAL-' . date('y') . '-' . str_pad($job_no, 6, '0', STR_PAD_LEFT);
                                            @endphp
                                            <input type="text" name="job_no" class="form-control"
                                                placeholder="Job No" value="{{ old('job_no', $job_no) }}" readonly>
                                        </td>
                                        <td class="create_label_column">Buyer</td>
                                        <td class="create_input_column">
                                            @php
                                                $buyers = App\Models\Buyer::all()->pluck('name', 'id');
                                            @endphp
                                            <select name="buyer_id" class="form-control">
                                                <option value="">Select Buyer</option>
                                                @foreach ($buyers as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ old('buyer_id') == $key ? 'selected' : '' }}>
                                                        {{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Style</td>
                                        <td class="create_input_column">
                                            {{-- @php
                                                $styles = DB::table('jobs')->select('style')->distinct()->get();
                                            @endphp

                                            <div class="form-group">
                                                <select id="style-select" name="style" class="form-control"
                                                    onchange="toggleInputField(this, 'style-input')">
                                                    <option value="">Select Style</option>
                                                    <option value="other"
                                                        {{ old('style') == 'other' ? 'selected' : '' }}>Other</option>
                                                    @foreach ($styles as $style)
                                                        <option value="{{ $style->style }}"
                                                            {{ old('style') == $style->style ? 'selected' : '' }}>
                                                            {{ $style->style }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" id="style-input" name="style_input"
                                                    class="form-control mt-2"
                                                    style="display:{{ old('style') == 'other' ? 'block' : 'none' }};"
                                                    value="{{ old('style_input') }}" placeholder="Enter new style">
                                            </div> --}}
                                            <input type="text" name="style" class="form-control"
                                                placeholder="Style" value="{{ old('style') }}" required>
                                        </td>
                                        <td class="create_label_column">PO</td>
                                        <td class="create_input_column">
                                            @php
                                                $pos = DB::table('jobs')->select('po')->distinct()->get();
                                            @endphp

                                            <div class="form-group mt-2">
                                                <select id="po-select" name="po" class="form-control"
                                                    onchange="toggleInputField(this, 'po-input')">
                                                    <option value="">Select PO</option>
                                                    <option value="other" {{ old('po') == 'other' ? 'selected' : '' }}>
                                                        Other</option>
                                                    @foreach ($pos as $po)
                                                        <option value="{{ $po->po }}"
                                                            {{ old('po') == $po->po ? 'selected' : '' }}>
                                                            {{ $po->po }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" id="po-input" name="po_input"
                                                    class="form-control mt-2"
                                                    style="display:{{ old('po') == 'other' ? 'block' : 'none' }};"
                                                    value="{{ old('po_input') }}" placeholder="Enter new PO">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Department</td>
                                        <td class="create_input_column">
                                            @php
                                                $departments = DB::table('jobs')
                                                    ->select('department')
                                                    ->distinct()
                                                    ->get();
                                            @endphp

                                            <div class="form-group mt-2">
                                                <select id="department-select" name="department" class="form-control"
                                                    onchange="toggleInputField(this, 'department-input')">
                                                    <option value="">Select Department</option>
                                                    <option value="other"
                                                        {{ old('department') == 'other' ? 'selected' : '' }}>Other
                                                    </option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->department }}"
                                                            {{ old('department') == $department->department ? 'selected' : '' }}>
                                                            {{ $department->department }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" id="department-input" name="department_input"
                                                    class="form-control mt-2"
                                                    style="display:{{ old('department') == 'other' ? 'block' : 'none' }};"
                                                    value="{{ old('department_input') }}"
                                                    placeholder="Enter new department">
                                            </div>
                                        </td>
                                        <td class="create_label_column">Item</td>
                                        <td class="create_input_column">
                                            <select id="item" name="item" class="form-control" required>
                                                <option value="">Select Item</option>
                                                <option value="T-shirt"
                                                    {{ old('item') == 'T-shirt' ? 'selected' : '' }}>T-shirt</option>
                                                <option value="Polo Shirt"
                                                    {{ old('item') == 'Polo Shirt' ? 'selected' : '' }}>Polo Shirt
                                                </option>
                                                <option value="Romper"
                                                    {{ old('item') == 'Romper' ? 'selected' : '' }}>Romper</option>
                                                <option value="Sweat Shirt"
                                                    {{ old('item') == 'Sweat Shirt' ? 'selected' : '' }}>Sweat Shirt
                                                </option>
                                                <option value="Jacket"
                                                    {{ old('item') == 'Jacket' ? 'selected' : '' }}>Jacket</option>
                                                <option value="Hoodie"
                                                    {{ old('item') == 'Hoodie' ? 'selected' : '' }}>Hoodie</option>
                                                <option value="Jogger"
                                                    {{ old('item') == 'Jogger' ? 'selected' : '' }}>Jogger</option>
                                                <option value="Pant/Bottom"
                                                    {{ old('item') == 'Pant/Bottom' ? 'selected' : '' }}>Pant/Bottom
                                                </option>
                                                <option value="Cargo Pant"
                                                    {{ old('item') == 'Cargo Pant' ? 'selected' : '' }}>Cargo Pant
                                                </option>
                                                <option value="Leggings"
                                                    {{ old('item') == 'Leggings' ? 'selected' : '' }}>Leggings</option>
                                                <option value="Ladies/Girls Dress"
                                                    {{ old('item') == 'Ladies/Girls Dress' ? 'selected' : '' }}>
                                                    Ladies/Girls Dress</option>
                                                <option value="Others"
                                                    {{ old('item') == 'Others' ? 'selected' : '' }}>Others</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Destination</td>
                                        <td class="create_input_column">
                                            <div class="form-group">

                                                <select id="country" name="country" class="form-control">
                                                    <option value="">Select Country</option>
                                                    <option value="Afghanistan">Afghanistan</option>
                                                    <option value="Albania">Albania</option>
                                                    <option value="Algeria">Algeria</option>
                                                    <option value="Andorra">Andorra</option>
                                                    <option value="Angola">Angola</option>
                                                    <option value="Argentina">Argentina</option>
                                                    <option value="Armenia">Armenia</option>
                                                    <option value="Australia">Australia</option>
                                                    <option value="Austria">Austria</option>
                                                    <option value="Azerbaijan">Azerbaijan</option>
                                                    <option value="Bahamas">Bahamas</option>
                                                    <option value="Bahrain">Bahrain</option>
                                                    <option value="Bangladesh">Bangladesh</option>
                                                    <option value="Barbados">Barbados</option>
                                                    <option value="Belarus">Belarus</option>
                                                    <option value="Belgium">Belgium</option>
                                                    <option value="Belize">Belize</option>
                                                    <option value="Benin">Benin</option>
                                                    <option value="Bhutan">Bhutan</option>
                                                    <option value="Bolivia">Bolivia</option>
                                                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina
                                                    </option>
                                                    <option value="Botswana">Botswana</option>
                                                    <option value="Brazil">Brazil</option>
                                                    <option value="Brunei">Brunei</option>
                                                    <option value="Bulgaria">Bulgaria</option>
                                                    <option value="Burkina Faso">Burkina Faso</option>
                                                    <option value="Burundi">Burundi</option>
                                                    <option value="Cambodia">Cambodia</option>
                                                    <option value="Cameroon">Cameroon</option>
                                                    <option value="Canada">Canada</option>
                                                    <option value="Cape Verde">Cape Verde</option>
                                                    <option value="Central African Republic">Central African Republic
                                                    </option>
                                                    <option value="Chad">Chad</option>
                                                    <option value="Chile">Chile</option>
                                                    <option value="China">China</option>
                                                    <option value="Colombia">Colombia</option>
                                                    <option value="Comoros">Comoros</option>
                                                    <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
                                                    <option value="Congo (Kinshasa)">Congo (Kinshasa)</option>
                                                    <option value="Costa Rica">Costa Rica</option>
                                                    <option value="Croatia">Croatia</option>
                                                    <option value="Cuba">Cuba</option>
                                                    <option value="Cyprus">Cyprus</option>
                                                    <option value="Czech Republic">Czech Republic</option>
                                                    <option value="Denmark">Denmark</option>
                                                    <option value="Djibouti">Djibouti</option>
                                                    <option value="Dominica">Dominica</option>
                                                    <option value="Dominican Republic">Dominican Republic</option>
                                                    <option value="East Timor (Timor Timur)">East Timor (Timor Timur)
                                                    </option>
                                                    <option value="Ecuador">Ecuador</option>
                                                    <option value="Egypt">Egypt</option>
                                                    <option value="El Salvador">El Salvador</option>
                                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                    <option value="Eritrea">Eritrea</option>
                                                    <option value="Estonia">Estonia</option>
                                                    <option value="Eswatini">Eswatini</option>
                                                    <option value="Ethiopia">Ethiopia</option>
                                                    <option value="Fiji">Fiji</option>
                                                    <option value="Finland">Finland</option>
                                                    <option value="France">France</option>
                                                    <option value="Gabon">Gabon</option>
                                                    <option value="Gambia">Gambia</option>
                                                    <option value="Georgia">Georgia</option>
                                                    <option value="Germany">Germany</option>
                                                    <option value="Ghana">Ghana</option>
                                                    <option value="Greece">Greece</option>
                                                    <option value="Grenada">Grenada</option>
                                                    <option value="Guatemala">Guatemala</option>
                                                    <option value="Guinea">Guinea</option>
                                                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                                                    <option value="Guyana">Guyana</option>
                                                    <option value="Haiti">Haiti</option>
                                                    <option value="Honduras">Honduras</option>
                                                    <option value="Hungary">Hungary</option>
                                                    <option value="Iceland">Iceland</option>
                                                    <option value="India">India</option>
                                                    <option value="Indonesia">Indonesia</option>
                                                    <option value="Iran">Iran</option>
                                                    <option value="Iraq">Iraq</option>
                                                    <option value="Ireland">Ireland</option>
                                                    <option value="Israel">Israel</option>
                                                    <option value="Italy">Italy</option>
                                                    <option value="Ivory Coast">Ivory Coast</option>
                                                    <option value="Jamaica">Jamaica</option>
                                                    <option value="Japan">Japan</option>
                                                    <option value="Jordan">Jordan</option>
                                                    <option value="Kazakhstan">Kazakhstan</option>
                                                    <option value="Kenya">Kenya</option>
                                                    <option value="Kiribati">Kiribati</option>
                                                    <option value="Korea, North">Korea, North</option>
                                                    <option value="Korea, South">Korea, South</option>
                                                    <option value="Kuwait">Kuwait</option>
                                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                    <option value="Laos">Laos</option>
                                                    <option value="Latvia">Latvia</option>
                                                    <option value="Lebanon">Lebanon</option>
                                                    <option value="Lesotho">Lesotho</option>
                                                    <option value="Liberia">Liberia</option>
                                                    <option value="Libya">Libya</option>
                                                    <option value="Liechtenstein">Liechtenstein</option>
                                                    <option value="Lithuania">Lithuania</option>
                                                    <option value="Luxembourg">Luxembourg</option>
                                                    <option value="Madagascar">Madagascar</option>
                                                    <option value="Malawi">Malawi</option>
                                                    <option value="Malaysia">Malaysia</option>
                                                    <option value="Maldives">Maldives</option>
                                                    <option value="Mali">Mali</option>
                                                    <option value="Malta">Malta</option>
                                                    <option value="Marshall Islands">Marshall Islands</option>
                                                    <option value="Mauritania">Mauritania</option>
                                                    <option value="Mauritius">Mauritius</option>
                                                    <option value="Mexico">Mexico</option>
                                                    <option value="Micronesia">Micronesia</option>
                                                    <option value="Moldova">Moldova</option>
                                                    <option value="Monaco">Monaco</option>
                                                    <option value="Mongolia">Mongolia</option>
                                                    <option value="Montenegro">Montenegro</option>
                                                    <option value="Morocco">Morocco</option>
                                                    <option value="Mozambique">Mozambique</option>
                                                    <option value="Myanmar">Myanmar</option>
                                                    <option value="Namibia">Namibia</option>
                                                    <option value="Nauru">Nauru</option>
                                                    <option value="Nepal">Nepal</option>
                                                    <option value="Netherlands">Netherlands</option>
                                                    <option value="New Zealand">New Zealand</option>
                                                    <option value="Nicaragua">Nicaragua</option>
                                                    <option value="Niger">Niger</option>
                                                    <option value="Nigeria">Nigeria</option>
                                                    <option value="North Macedonia">North Macedonia</option>
                                                    <option value="Norway">Norway</option>
                                                    <option value="Oman">Oman</option>
                                                    <option value="Pakistan">Pakistan</option>
                                                    <option value="Palau">Palau</option>
                                                    <option value="Panama">Panama</option>
                                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                                    <option value="Paraguay">Paraguay</option>
                                                    <option value="Peru">Peru</option>
                                                    <option value="Philippines">Philippines</option>
                                                    <option value="Poland">Poland</option>
                                                    <option value="Portugal">Portugal</option>
                                                    <option value="Qatar">Qatar</option>
                                                    <option value="Romania">Romania</option>
                                                    <option value="Russia">Russia</option>
                                                    <option value="Rwanda">Rwanda</option>
                                                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis
                                                    </option>
                                                    <option value="Saint Lucia">Saint Lucia</option>
                                                    <option value="Saint Vincent and the Grenadines">Saint Vincent and
                                                        the Grenadines</option>
                                                    <option value="Samoa">Samoa</option>
                                                    <option value="San Marino">San Marino</option>
                                                    <option value="Sao Tome and Principe">Sao Tome and Principe
                                                    </option>
                                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                                    <option value="Senegal">Senegal</option>
                                                    <option value="Serbia">Serbia</option>
                                                    <option value="Seychelles">Seychelles</option>
                                                    <option value="Sierra Leone">Sierra Leone</option>
                                                    <option value="Singapore">Singapore</option>
                                                    <option value="Slovakia">Slovakia</option>
                                                    <option value="Slovenia">Slovenia</option>
                                                    <option value="Solomon Islands">Solomon Islands</option>
                                                    <option value="Somalia">Somalia</option>
                                                    <option value="South Africa">South Africa</option>
                                                    <option value="South Sudan">South Sudan</option>
                                                    <option value="Spain">Spain</option>
                                                    <option value="Sri Lanka">Sri Lanka</option>
                                                    <option value="Sudan">Sudan</option>
                                                    <option value="Suriname">Suriname</option>
                                                    <option value="Sweden">Sweden</option>
                                                    <option value="Switzerland">Switzerland</option>
                                                    <option value="Syria">Syria</option>
                                                    <option value="Taiwan">Taiwan</option>
                                                    <option value="Tajikistan">Tajikistan</option>
                                                    <option value="Tanzania">Tanzania</option>
                                                    <option value="Thailand">Thailand</option>
                                                    <option value="Togo">Togo</option>
                                                    <option value="Tonga">Tonga</option>
                                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                    <option value="Tunisia">Tunisia</option>
                                                    <option value="Turkey">Turkey</option>
                                                    <option value="Turkmenistan">Turkmenistan</option>
                                                    <option value="Tuvalu">Tuvalu</option>
                                                    <option value="Uganda">Uganda</option>
                                                    <option value="Ukraine">Ukraine</option>
                                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                                    <option value="United Kingdom">United Kingdom</option>
                                                    <option value="United States">United States</option>
                                                    <option value="Uruguay">Uruguay</option>
                                                    <option value="Uzbekistan">Uzbekistan</option>
                                                    <option value="Vanuatu">Vanuatu</option>
                                                    <option value="Vatican City">Vatican City</option>
                                                    <option value="Venezuela">Venezuela</option>
                                                    <option value="Vietnam">Vietnam</option>
                                                    <option value="Yemen">Yemen</option>
                                                    <option value="Zambia">Zambia</option>
                                                    <option value="Zimbabwe">Zimbabwe</option>
                                                </select>
                                            </div>

                                        </td>
                                        <td class="create_label_column">Order Quantity</td>
                                        <td class="create_input_column">
                                            <input type="number" name="order_quantity" class="form-control"
                                                placeholder="Order Quantity" value="{{ old('order_quantity') ?? 0 }}"
                                                required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>


                            <table class="table table-bordered mt-2" id="colorWayTable">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        {{-- <th>Size</th> --}}
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (old('color'))
                                        @foreach (old('color') as $index => $color)
                                            <tr>
                                                <td>
                                                    <input type="text" name="color[]" class="form-control"
                                                        placeholder="Color" value="{{ $color }}" required>
                                                </td>
                                                <input type="hidden" name="size[]" class="form-control"
                                                    placeholder="Size" value="{{ old('size')[$index] ?? 'ALL' }}"
                                                    required readonly>
                                                {{-- <td>
                                                   
                                                    <select name="size[]" class="form-control" required>
                                                        <option value="">Select Size</option>
                                                        <option value="XS"
                                                            {{ old('size')[$index] == 'XS' ? 'selected' : '' }}>XS
                                                        </option>
                                                        <option value="S"
                                                            {{ old('size')[$index] == 'S' ? 'selected' : '' }}>S
                                                        </option>
                                                        <option value="M"
                                                            {{ old('size')[$index] == 'M' ? 'selected' : '' }}>M
                                                        </option>
                                                        <option value="L"
                                                            {{ old('size')[$index] == 'L' ? 'selected' : '' }}>L
                                                        </option>
                                                        <option value="XL"
                                                            {{ old('size')[$index] == 'XL' ? 'selected' : '' }}>XL
                                                        </option>
                                                        <option value="XXL"
                                                            {{ old('size')[$index] == 'XXL' ? 'selected' : '' }}>XXL
                                                        </option>
                                                        <option value="XXXL"
                                                            {{ old('size')[$index] == 'XXXL' ? 'selected' : '' }}>XXXL
                                                        </option>
                                                        <option value="XXXXL"
                                                            {{ old('size')[$index] == 'XXXXL' ? 'selected' : '' }}>
                                                            XXXXL</option>
                                                    </select> 
                                                </td> --}}
                                                <td>
                                                    <input type="number" name="color_quantity[]"
                                                        class="form-control" placeholder="Quantity"
                                                        value="{{ old('color_quantity')[$index] }}" required>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-outline-primary"
                                                        id="addColorWay">Add</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="text" name="color[]" class="form-control"
                                                    placeholder="Color" required>
                                            </td>
                                            <input type="hidden" name="size[]" class="form-control"
                                                placeholder="Size" required value="ALL" readonly>
                                            {{-- <td>
                                                
                                                 <select name="size[]" class="form-control" required>
                                                    <option value="">Select Size</option>
                                                    <option value="XS">XS</option>
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXL">XXL</option>
                                                    <option value="XXXL">XXXL</option>
                                                    <option value="XXXXL">XXXXL</option>
                                                    <option value="other_size">ADD NEW SIZE</option>
                                                </select>
                                                <script>
                                                    $(document).ready(function() {
                                                        $('select[name="size[]"]').change(function() {
                                                            if ($(this).val() == 'other_size') {
                                                                $(this).after('<input type="text" name="size[]" class="form-control mt-2" placeholder="Enter new size">');
                                                                $(this).remove();
                                                            }
                                                        });
                                                    });
                                                </script> 
                                            </td> --}}
                                            <td>
                                                <input type="number" name="color_quantity[]" class="form-control"
                                                    placeholder="Quantity" required>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-outline-primary"
                                                    id="addColorWay">Add</a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <a href="javascript:void(0)" class="btn btn-outline-primary"
                                                id="saveQuantity">Save Quantity</a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <table class="table">
                                <tr>
                                    {{-- <td class="create_label_column">Inspection Date</td>
                                    <td class="create_input_column">
                                        <input type="date" name="ins_date" class="form-control"
                                            value="{{ old('ins_date') }}">
                                    </td> --}}

                                    <td class="create_label_column">Order / PO Received Date</td>
                                    <td class="create_input_column">
                                        <input type="date" name="order_received_date" class="form-control"
                                            value="{{ old('order_received_date') }}" id="order_received_date">
                                    </td>

                                    <td class="create_label_column">Shipment / Delivery Date</td>
                                    <td class="create_input_column">
                                        <input type="date" name="delivery_date" class="form-control"
                                            value="{{ old('delivery_date') }}" id="delivery_date">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Target SMV</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="target_smv" class="form-control"
                                            placeholder="Target SMV" value="{{ old('target_smv') }}">
                                    </td>
                                    <td class="create_label_column">Production Minutes</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="production_minutes"
                                            class="form-control" placeholder="Production Minutes"
                                            value="{{ old('production_minutes') }}" required readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Unit Price</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="unit_price" class="form-control"
                                            placeholder="Unit Price" value="{{ old('unit_price') }}">
                                    </td>
                                    <td class="create_label_column">Total Value</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="total_value" class="form-control"
                                            placeholder="Total Value" value="{{ old('total_value') }}" required
                                            readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">CM/PC</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="cm_pc" class="form-control"
                                            placeholder="CM/PC" value="{{ old('cm_pc') }}">
                                    </td>
                                    <td class="create_label_column">Total CM</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="total_cm" class="form-control"
                                            placeholder="Total CM" value="{{ old('total_cm') }}" required readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Consumption/Dzn</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="consumption_dzn"
                                            class="form-control" placeholder="Consumption/Dzn"
                                            value="{{ old('consumption_dzn') }}">
                                    </td>
                                    <td class="create_label_column">Fabric Quantity</td>
                                    <td class="create_input_column">
                                        <input type="number" step="0.01" name="fabric_qnty" class="form-control"
                                            placeholder="Fabric Quantity" value="{{ old('fabric_qnty') }}" required
                                            readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Fabrication</td>
                                    <td class="create_input_column">
                                        <input type="text" name="fabrication" class="form-control"
                                            placeholder="Fabrication" value="{{ old('fabrication') }}">
                                    </td>
                                    <!-- Wash Dropdown -->
                                    <td class="create_label_column">Wash</td>
                                    <td class="create_input_column">
                                        <select id="wash" name="wash" class="form-control">
                                            <option value="No Wash" {{ old('wash') == 'No Wash' ? 'selected' : '' }}>
                                                No Wash</option>
                                            <option value="Normal Wash"
                                                {{ old('wash') == 'Normal Wash' ? 'selected' : '' }}>Normal Wash
                                            </option>
                                            <option value="Semi-Critical Wash"
                                                {{ old('wash') == 'Semi-Critical Wash' ? 'selected' : '' }}>
                                                Semi-Critical Wash</option>
                                            <option value="Critical Wash"
                                                {{ old('wash') == 'Critical Wash' ? 'selected' : '' }}>Critical Wash
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">AOP</td>
                                    <td class="create_input_column">
                                        <select name="aop" class="form-control">
                                            {{-- <option value="">Select AOP</option> --}}
                                            <option value="No" {{ old('aop') == 'No' ? 'selected' : '' }}>No
                                            </option>
                                            <option value="Yes" {{ old('aop') == 'Yes' ? 'selected' : '' }}>Yes
                                            </option>
                                            {{-- <option value="No" {{ old('aop') == 'No' ? 'selected' : '' }}>No
                                            </option> --}}
                                        </select>
                                    </td>
                                    <!-- Print Dropdown -->
                                    <td class="create_label_column">Print</td>
                                    <td class="create_input_column">
                                        <select id="print" name="print" class="form-control">
                                            <option value="No Print"
                                                {{ old('print') == 'No Print' ? 'selected' : '' }}>No Print</option>
                                            <option value="Chest Print"
                                                {{ old('print') == 'Chest Print' ? 'selected' : '' }}>Chest Print
                                            </option>
                                            <option value="Neck Print"
                                                {{ old('print') == 'Neck Print' ? 'selected' : '' }}>Neck Print
                                            </option>
                                            <option value="Both Print"
                                                {{ old('print') == 'Both Print' ? 'selected' : '' }}>Both Print
                                            </option>
                                        </select>
                                    </td>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Embroidery</td>
                                    <td class="create_input_column">
                                        <select name="embroidery" class="form-control">
                                            {{-- <option value="">Select Embroidery</option> --}}
                                            <option value="No" {{ old('embroidery') == 'No' ? 'selected' : '' }}>
                                                No</option>
                                            <option value="Yes" {{ old('embroidery') == 'Yes' ? 'selected' : '' }}>
                                                Yes</option>
                                            {{-- <option value="No" {{ old('embroidery') == 'No' ? 'selected' : '' }}>
                                                No</option> --}}
                                        </select>
                                    </td>
                                    <td class="create_label_column">Lead Time</td>
                                    <td class="create_input_column">
                                        <input type="number" name="total_lead_time" class="form-control"
                                            placeholder="Lead Time" value="{{ old('total_lead_time') }}" readonly
                                            id="total_lead_time">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="create_label_column">Remarks</td>
                                    <td class="create_input_column">
                                        <textarea name="remarks" class="form-control" placeholder="Remarks">{{ old('remarks') }}</textarea>
                                    </td>
                                    <!-- Print/Wash Dropdown -->
                                    <td class="create_label_column">Print/Wash</td>
                                    <td class="create_input_column">
                                        {{-- <select id="print_wash" name="print_wash" class="form-control" required
                                            readonly>
                                            <option value="">Select Print/Wash</option>
                                            <option value="No Print and Wash"
                                                {{ old('print_wash') == 'No Print and Wash' ? 'selected' : '' }}>No
                                                Print and Wash</option>
                                            <option value="Only Print"
                                                {{ old('print_wash') == 'Only Print' ? 'selected' : '' }}>Only Print
                                            </option>
                                            <option value="Only Wash"
                                                {{ old('print_wash') == 'Only Wash' ? 'selected' : '' }}>Only Wash
                                            </option>
                                            <option value="Both Print and Wash"
                                                {{ old('print_wash') == 'Both Print and Wash' ? 'selected' : '' }}>Both
                                                Print and Wash</option>
                                        </select> --}}
                                        <input type="text" name="print_wash" class="form-control"
                                            placeholder="Print/Wash" value="{{ old('print_wash') }}" required
                                            readonly id="print_wash">
                                    </td>
                                </tr>
                                </tbody>
                            </table>


                            <div class="button-container">
                                <button type="submit" id="saveButton" class="btn btn-outline-success">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <button type="reset" class="btn btn-outline-danger" id="resetButton">
                                    <i class="fas fa-undo"></i> Reset </button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary"
                                    id="cancelButton">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Cookie handling functions
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        function saveFormDataToCookie() {
            const formElements = document.querySelectorAll('form input, form select, form textarea');
            const formData = {};

            // Group elements by name and handle array fields
            const elementsByName = {};
            formElements.forEach(element => {
                if (!element.name) return;
                if (!elementsByName[element.name]) {
                    elementsByName[element.name] = [];
                }
                elementsByName[element.name].push(element);
            });

            Object.entries(elementsByName).forEach(([name, elements]) => {
                if (name.endsWith('[]')) {
                    formData[name] = elements.map(el => el.value);
                } else {
                    formData[name] = elements[0].value;
                }
            });

            document.cookie =
                `formData=${JSON.stringify(formData)}; expires=${new Date(Date.now() + 86400e3).toUTCString()}; path=/`;
        }

        function loadFormDataFromCookie() {
            const formDataJson = getCookie('formData');
            if (!formDataJson) return;

            const formData = JSON.parse(formDataJson);

            // Handle colorWayTable dynamic rows
            if (formData['color[]'] && Array.isArray(formData['color[]'])) {
                const tbody = document.querySelector('#colorWayTable tbody');
                tbody.innerHTML = ''; // Clear existing rows

                formData['color[]'].forEach((color, index) => {
                    const newRowHtml = `
                    <tr>
                        <td><input type="text" name="color[]" class="form-control" placeholder="Color" required></td>
                        <input type="hidden" name="size[]" value="${formData['size[]']?.[index] || 'ALL'}">
                        <td><input type="number" name="color_quantity[]" class="form-control" placeholder="Quantity" required></td>
                        <td><a href="javascript:void(0)" class="btn btn-outline-danger" id="removeColorWay">Remove</a></td>
                    </tr>
                `;
                    tbody.insertAdjacentHTML('beforeend', newRowHtml);
                    const newRow = tbody.lastElementChild;
                    newRow.querySelector('input[name="color[]"]').value = color;
                    newRow.querySelector('input[name="color_quantity[]"]').value = formData['color_quantity[]']?.[
                        index
                    ] || '';
                });

                // Show save button if there are entries
                if (formData['color[]'].length > 0) {
                    document.getElementById('saveButton').style.display = 'block';
                }
            }

            // Handle other form fields
            Object.entries(formData).forEach(([name, value]) => {
                if (name.endsWith('[]')) return;

                const elements = document.querySelectorAll(`[name="${name}"]`);
                if (elements.length > 0) {
                    elements.forEach(element => {
                        if (element.type !== 'file') { // Skip file inputs
                            element.value = value;
                            if ($(element).hasClass('select2-hidden-accessible')) {
                                $(element).trigger('change');
                            }
                        }
                    });
                }
            });
        }

        // Save form data on any input
        const form = document.querySelector('form');
        form.addEventListener('input', () => saveFormDataToCookie());
        form.addEventListener('change', () => saveFormDataToCookie());

        // Load saved data when page loads
        window.addEventListener('load', () => {
            loadFormDataFromCookie();
            // Initialize calculations with loaded data
            calculateProduction();
            calculateTotalValue();
            calculateTotalCM();
            calculateFabricQuantity();
        });

        // Clear cookie on form submit
        form.addEventListener('submit', () => {
            document.cookie = 'formData=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });

        // Clear cookie on reset or cancel
        document.getElementById('cancelButton').addEventListener('click', () => {
            document.cookie = 'formData=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });

        document.getElementById('resetButton').addEventListener('click', () => {
            document.cookie = 'formData=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        });

        $(document).ready(function() {
            function updatePrintWashDropdown() {
                var print = $('#print').val();
                var wash = $('#wash').val();

                if ((print === 'Chest Print' || print === 'Neck Print' || print === 'Both Print') && wash ===
                    'No Wash') {
                    $('#print_wash').val('Only Print');
                } else if (print === 'No Print' && (wash === 'Normal Wash' || wash === 'Semi-Critical Wash' ||
                        wash === 'Critical Wash')) {
                    $('#print_wash').val('Only Wash');
                } else if (print === 'No Print' && wash === 'No Wash') {
                    $('#print_wash').val('No Print and Wash');
                } else if ((print === 'Chest Print' || print === 'Neck Print' || print === 'Both Print') && (
                        wash === 'Normal Wash' || wash === 'Semi-Critical Wash' || wash === 'Critical Wash')) {
                    $('#print_wash').val('Both Print and Wash');
                } else {
                    $('#print_wash').val('');
                }
            }

            // Initial update
            updatePrintWashDropdown();

            // Update on change of print or wash dropdown
            $('#print, #wash').change(function() {
                updatePrintWashDropdown();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#order_received_date').change(function() {
                var order_received_date = new Date($('#order_received_date').val());
                var delivery_date = new Date($('#delivery_date').val());
                var diffTime = Math.abs(delivery_date - order_received_date);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                $('#total_lead_time').val(diffDays);
            });
            $('#delivery_date').change(function() {
                var order_received_date = new Date($('#order_received_date').val());
                var delivery_date = new Date($('#delivery_date').val());
                var diffTime = Math.abs(delivery_date - order_received_date);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                $('#total_lead_time').val(diffDays);
            });
        });

        $(document).ready(function() {

            $('#style-select').select2();
            $('#po-select').select2();
            $('#department-select').select2();
            $('#country').select2();

        })
        $(document).ready(function() {

            $('#colorWayTable').on('click', '#removeColorWay', function() {
                $(this).closest('tr').remove();
            });

            // if any colorWayTable row field is empty then saveQuantity button will be disabled
            $('#colorWayTable tbody tr').each(function() {
                var color = $(this).find('input[name="color[]"]').val();
                var size = $(this).find('input[name="size[]"]').val();
                var quantity = $(this).find('input[name="color_quantity[]"]').val();
                if (color == '' || size == '' || quantity == '') {
                    $('#saveQuantity').prop('disabled', true);
                }
            });

            $('#saveQuantity').click(function() {
                var order_quantity = 0;
                $('#colorWayTable tbody tr').each(function() {
                    var quantity = parseInt($(this).find('input[name="color_quantity[]"]').val());
                    if (!isNaN(quantity)) {
                        order_quantity += quantity;
                    }
                });
                $('input[name="order_quantity"]').val(order_quantity).prop('readonly', true);
                $('#colorWayModal').modal('hide');
            });

            $('#colorWayTable').on('click', '#addColorWay', function() {
                var newRow = `<tr>
                <td><input type="text" name="color[]" class="form-control" placeholder="Color" required></td>
                <input type="hidden" name="size[]" class="form-control"
                                                    placeholder="Size" required value="ALL" readonly>
                <td><input type="number" name="color_quantity[]" class="form-control" placeholder="Quantity" required></td>
                <td><a href="javascript:void(0)" class="btn btn-outline-danger" id="removeColorWay">Remove</a></td>
            </tr>`;
                $('#colorWayTable tbody').append(newRow);
            });
        });

        // saveButton hide until saveQuantity button click 
        document.getElementById('saveButton').style.display = 'none';
        document.getElementById('saveQuantity').addEventListener('click', function() {
            document.getElementById('saveButton').style.display = 'block';
        });
        //after saveButton click hide saveButton
        document.getElementById('saveButton').addEventListener('click', function() {
            document.getElementById('saveButton').style.display = 'none';
        });

        // Function to toggle input field visibility
        function toggleInputField(select, inputId) {
            var inputField = document.getElementById(inputId);
            if (select.value === 'other') {
                inputField.style.display = 'block';
                inputField.name = select.name;
                select.name = select.id + '_select';
            } else {
                inputField.style.display = 'none';
                inputField.name = inputId.replace('-input', '_input');
                select.name = select.id.replace('-select', '');
            }
        }

        // JavaScript for calculations
        function calculateProduction() {
            var orderQuantity = parseFloat(document.querySelector('input[name="order_quantity"]').value);
            var targetSMV = parseFloat(document.querySelector('input[name="target_smv"]').value);
            var productionMinutes = orderQuantity * targetSMV;
            document.querySelector('input[name="production_minutes"]').value = productionMinutes.toFixed(3);
        }

        function calculateTotalValue() {
            var orderQuantity = parseFloat(document.querySelector('input[name="order_quantity"]').value);
            var unitPrice = parseFloat(document.querySelector('input[name="unit_price"]').value);
            var totalValue = orderQuantity * unitPrice;
            document.querySelector('input[name="total_value"]').value = totalValue.toFixed(3);
        }

        function calculateTotalCM() {
            var orderQuantity = parseFloat(document.querySelector('input[name="order_quantity"]').value);
            var cmPC = parseFloat(document.querySelector('input[name="cm_pc"]').value);
            var totalCM = orderQuantity * cmPC;
            document.querySelector('input[name="total_cm"]').value = totalCM.toFixed(3);
        }

        function calculateFabricQuantity() {
            var orderQuantity = parseFloat(document.querySelector('input[name="order_quantity"]').value);
            var consumptionDzn = parseFloat(document.querySelector('input[name="consumption_dzn"]').value);
            var fabricQuantity = orderQuantity * (consumptionDzn / 12);
            document.querySelector('input[name="fabric_qnty"]').value = Math.ceil(fabricQuantity);
        }

        // Event listeners for input changes
        document.querySelector('input[name="order_quantity"]').addEventListener('input', function() {
            calculateProduction();
            calculateTotalValue();
            calculateTotalCM();
            calculateFabricQuantity();
        });

        document.querySelector('input[name="target_smv"]').addEventListener('input', function() {
            calculateProduction();
        });

        document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
            calculateTotalValue();
        });

        document.querySelector('input[name="cm_pc"]').addEventListener('input', function() {
            calculateTotalCM();
        });

        document.querySelector('input[name="consumption_dzn"]').addEventListener('input', function() {
            calculateFabricQuantity();
        });

        // // save all form data to the browser until savebutton click to submit the form and if any form input chage before save the update that to browser and after submit the form clear the browser data
        // var form = document.querySelector('form');
        // form.addEventListener('input', function() {
        //     var formData = new FormData(form);
        //     for (var pair of formData.entries()) {
        //         localStorage.setItem(pair[0], pair[1]);
        //     }
        // });
        // form.addEventListener('submit', function() {
        //     localStorage.clear();
        // });

        // // Load saved data from browser
        // window.addEventListener('load', function() {
        //     var form = document.querySelector('form');
        //     var formData = new FormData(form);
        //     for (var pair of formData.entries()) {
        //         var input = document.querySelector(`[name="${pair[0]}"]`);
        //         if (input) {
        //             input.value = pair[1];
        //         }
        //     }
        // });

        // //if click cancel button or reset button then also clear browser data
        // document.getElementById('cancelButton').addEventListener('click', function() {
        //     localStorage.clear();
        // });
        // document.getElementById('resetButton').addEventListener('click', function() {
        //     localStorage.clear();
        // });

       
    </script>



</x-backend.layouts.master>
