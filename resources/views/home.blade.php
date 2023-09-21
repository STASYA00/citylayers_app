@php
    $locale = session()->get('locale');
    if ($locale == null) {
        $locale = 'en';
    }
@endphp
@extends('layouts.app')

@section('main')
    <div data-barba="container">
        <div class="flex flex-col mx-auto">
            <div class="">
                <div class="relative">
                    <div class="" x-cloak x-data="{ show: false }">
                        <div class="absolute z-20 p-2 bg-black rounded-lg top-8 left-4" @click="show=true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-8 h-8 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>

                        <div x-show="show" @click.outside="show=false"
                            class="z-30 absolute top-20 left-4 w-[60%] bg-white divide-y divide-gray-100 rounded-lg shadow  dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="dropdownDefaultButton">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Dashboard</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Earnings</a>
                                </li>
                                <li>
                                    <a href="/admin/logout"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Sign
                                        out</a>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <div class="fixed z-20 flex items-center justify-center p-4 bg-black rounded-full bottom-28 left-4">
                        <img src="{{ asset('img/triangle.png') }}" class="w-7 h-7" alt="">
                    </div>
                    <a href="/filter"
                        class="fixed z-20 flex items-center justify-center p-4 bg-white border-2 border-black rounded-full bottom-28 right-4">
                        <img src="{{ asset('img/icons/eye-icon.jpg') }}" class="w-7 h-7" alt="">
                    </a>

                    <div id="map" class="absolute w-[100vw] z-10 h-[90vh]"></div>
                </div>
                <div x-data="{ modelOpen: false }">
                    <div
                        class="fixed bottom-0 z-50 w-full pt-3 pb-8 text-xl font-semibold text-center shadow-xl rounded-t-3xl bg-gray-50 ">

                        <div class="flex items-center justify-center">
                            <div class="py-0.5 w-14 mb-7 rounded-full px-4 bg-black">

                            </div>

                        </div>


                        <div class="flex">
                            <div class="absolute left-16 bottom-[18px] ">
                                <div class="p-3 bg-yellow-300 border-2 border-black rounded-full">
                                    <img src="{{ asset('img/search-icon.png') }}" class="w-7 h-7" alt="">
                                </div>

                            </div>
                            <div class="absolute left-6 bottom-[18px] ">
                                <div class="p-3 bg-blue-500 border-2 border-black rounded-full">
                                    <img src="{{ asset('img/image.png') }}" class="w-7 h-7" alt="">
                                </div>

                            </div>
                        </div>

                        <a class="w-full pt-4" @click="modelOpen =!modelOpen">
                            <span class="w-full px-12 py-4 text-white bg-blue-500 rounded-3xl">
                                Add on map
                            </span>

                        </a>

                        <div class="absolute right-5 bottom-[25px] ">
                            <div class="p-1 bg-blue-500 rounded-full w-9">
                                <span class="italic font-bold text-white">
                                    i
                                </span>
                            </div>
                        </div>

                    </div>


                    <div x-cloak x-show="modelOpen " class="fixed bottom-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center text-center">
                            <div x-cloak @click="modelOpen = false" x-show="modelOpen"
                                x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-40" aria-hidden="true">
                            </div>

                            <div x-cloak x-show="modelOpen" x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="z-50 w-screen p-4 transition-all transform bg-white h-[50%] shadow-xl mt-60 rounded-t-3xl">

                                <div class="px-1 pt-4" x-data="{ tab: 'place' }">

                                    <input type="text" class="w-full px-2 py-2 bg-white rounded-full"
                                        placeholder="Choose tags or add new city layers" name="input" id="">



                                    <div class="flex items-center justify-center mt-12">
                                        <div class="-mr-2 cursor-pointer" @click="tab='place'">
                                            <div class="flex flex-col w-[75px] justify-center items-center">
                                                <div class="bg-blue-500 border-2 border-black rounded-full shadow-xl"
                                                    :class="tab == 'place' || tab == 'place1' ?
                                                        'bg-blue-500 z-10 p-[22px]' :
                                                        'bg-blue-500/70 p-[35px]'">
                                                    <img src="{{ asset('img/image.png') }}" class="w-7 h-7"
                                                        :class="tab == 'place' || tab == 'place1' ? 'block' : 'hidden'"
                                                        id="place" alt="">
                                                </div>
                                                <div class="pl-2 font-semibold text-justify"
                                                    :class="tab == 'place' ? 'text-black' : 'text-black/50'">
                                                    Browse Places
                                                </div>
                                            </div>
                                        </div>
                                        <div class="-ml-2 cursor-pointer" @click="tab='observation'">
                                            <div class="flex flex-col w-[75px] justify-center items-center">

                                                <div class="flex items-center justify-center border-2 border-black rounded-full shadow-xl"
                                                    :class="tab == 'observation' || tab == 'observation1' ?
                                                        'bg-yellow-300 z-10 p-[16px]' :
                                                        'bg-yellow-300/70 p-[35px]'">
                                                    <span class="flex items-center justify-center w-10 h-10"
                                                        :class="tab == 'observation' || tab == 'observation1' ? 'block' :
                                                            'hidden'"
                                                        id="observation" alt="">🔍</span>
                                                </div>

                                                <div class="pl-8 font-semibold text-justify"
                                                    :class="tab == 'observation' ? 'text-black' : 'text-black/50'">
                                                    Browse Observation
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="flex items-center justify-center mt-40 italic font-semibold">
                                        <div class="absolute left-[52%] top-[170px] cursor-pointer" @click="tab='feature'"
                                            onclick="place()">


                                            <div class="w-8" :class="tab == 'feature' ? 'text-black' : 'text-black/50'">
                                                Browse Observation
                                            </div>

                                        </div>
                                        <div class="absolute left-[35%] top-[170px] cursor-pointer" @click="tab='place'"
                                            onclick="feature()">

                                            <div class="w-8" :class="tab == 'place' ? 'text-black' : 'text-black/50'">
                                                Browse Places
                                            </div>




                                        </div>


                                    </div> --}}



                                    <div class="flex items-center justify-center gap-10 mt-6 italic font-semibold"
                                        x-show="tab=='place'">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="rounded-full bg-blue-500  p-[35px] focus:ring-offset-2 focus:ring-blue-400"
                                                x-on:click="tab='observation'"
                                                onclick="select_place({{ $allPlaces[0]->id }})">

                                            </div>
                                            <span class="mt-2 text-black">{{ $allPlaces[0]->name }}</span>
                                        </div>

                                        <div class="flex flex-col items-center justify-center">
                                            <div class="rounded-full bg-blue-500  p-[35px]" x-on:click="tab='observation'"
                                                onclick="select_place({{ $allPlaces[1]->id }})">

                                            </div>
                                            <span class="mt-2 text-black">{{ $allPlaces[1]->name }}</span>
                                        </div>

                                        <div class="flex flex-col items-center justify-center">
                                            <button onclick="see()">
                                                <div class="rounded-full border-blue-500 border-2  p-[22px]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"
                                                        class="w-6 h-6 font-bold text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v12m6-6H6" />
                                                    </svg>

                                                </div>
                                                <span class="mt-2 text-black">See more</span>
                                            </button>
                                        </div>





                                    </div>
                                    <div class="flex items-center justify-center gap-10 mt-6 italic font-semibold"
                                        x-show="tab=='observation'">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="rounded-full bg-yellow-300  p-[35px]" x-on:click="tab='place'"
                                                onclick="select_observation({{ $allObservations[0]->id }})">

                                            </div>
                                            <span class="mt-2 text-black">{{ $allObservations[0]->name }}</span>
                                        </div>

                                        <div class="flex flex-col items-center justify-center">
                                            <div class="rounded-full bg-yellow-300  p-[35px]" x-on:click="tab='place'"
                                                onclick="select_observation({{ $allObservations[1]->id }})">

                                            </div>
                                            <span class="mt-2 text-black">{{ $allObservations[1]->name }}</span>
                                        </div>

                                        <div class="flex flex-col items-center justify-center">
                                            <button onclick="see()">
                                                <div class="rounded-full border-yellow-300 border-2  p-[22px]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"
                                                        class="w-6 h-6 font-bold text-yellow-300">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v12m6-6H6" />
                                                    </svg>

                                                </div>
                                                <span class="mt-2 text-black">See more</span>
                                            </button>

                                        </div>





                                    </div>



                                    <div class="flex items-center justify-center mt-8">
                                        <a href="/add-new-place"
                                            class="flex items-center justify-center gap-2 px-4 py-3 text-lg font-extrabold text-white bg-blue-500 rounded-3xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="3" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Add new</a>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="myfeel2" x-data="{ seeMore: false }">
                        <button id="othertag" @click="seeMore =!seeMore" class="hidden"></button>

                        <div x-cloak x-show="seeMore" class="absolute inset-0 z-[60] bg-white"
                            aria-labelledby="modal-title" role="dialog" aria-modal="true">

                            <div x-cloak x-show="seeMore" x-transition:enter="transition ease-out duration-300 transform"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="transition ease-in duration-200 transform"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="z-50 w-full max-w-xl transition-all transform">

                                <div class="px-4 pt-4" x-data="{ tab: 'place' }">
                                    <div @click="seeMore=false"
                                        class="flex justify-start items-start bg-black my-4 mx-2 p-1.5 w-7 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                        </svg>

                                    </div>

                                    <input type="text" class="w-full px-2 py-2 bg-white rounded-full"
                                        placeholder="Choose tags or add new city layers" name="input" id="">



                                    <div class="flex items-center justify-center mt-12">
                                        <div class="-mr-2 cursor-pointer" @click="tab='place'" onclick="place()">
                                            <div class="flex flex-col w-[75px] justify-center items-center">
                                                <div class="bg-blue-500 border-2 border-black rounded-full shadow-xl"
                                                    :class="tab == 'place' || tab == 'place1' ?
                                                        'bg-blue-500 z-10 p-[22px]' :
                                                        'bg-blue-500/70 p-[35px]'">
                                                    <img src="{{ asset('img/image.png') }}" class="w-7 h-7"
                                                        :class="tab == 'place' || tab == 'place1' ? 'block' : 'hidden'"
                                                        id="place" alt="">
                                                </div>
                                                <div class="pl-2 font-semibold text-justify"
                                                    :class="tab == 'place' ? 'text-black' : 'text-black/50'">
                                                    Browse Places
                                                </div>
                                            </div>
                                        </div>
                                        <div class="-ml-2 cursor-pointer" @click="tab='observation'"
                                            onclick="observation()">
                                            <div class="flex flex-col w-[75px] justify-center items-center">

                                                <div class="flex items-center justify-center border-2 border-black rounded-full shadow-xl"
                                                    :class="tab == 'observation' || tab == 'observation1' ?
                                                        'bg-yellow-300 z-10 p-[16px]' :
                                                        'bg-yellow-300/70 p-[35px]'">
                                                    <span class="flex items-center justify-center w-10 h-10"
                                                        :class="tab == 'observation' || tab == 'observation1' ? 'block' :
                                                            'hidden'"
                                                        id="observation" alt="">🔍</span>
                                                </div>

                                                <div class="pl-8 font-semibold text-justify"
                                                    :class="tab == 'observation' ? 'text-black' : 'text-black/50'">
                                                    Browse Observation
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="flex items-center justify-center mt-40 italic font-semibold">
                                        <div class="absolute left-[52%] top-[170px] cursor-pointer" @click="tab='feature'"
                                            onclick="place()">


                                            <div class="w-8" :class="tab == 'feature' ? 'text-black' : 'text-black/50'">
                                                Browse Observation
                                            </div>

                                        </div>
                                        <div class="absolute left-[35%] top-[170px] cursor-pointer" @click="tab='place'"
                                            onclick="feature()">

                                            <div class="w-8" :class="tab == 'place' ? 'text-black' : 'text-black/50'">
                                                Browse Places
                                            </div>




                                        </div>


                                    </div> --}}



                                    <div class="flex flex-col items-center justify-center gap-10 mt-6 italic font-semibold"
                                        x-show="tab=='place'">
                                        <div class="grid grid-cols-3 gap-8">
                                            @foreach ($allPlaces as $pls)
                                                <div class="flex flex-col items-center justify-center w-[80px]">
                                                    <div class="rounded-full bg-blue-500  p-[35px]"
                                                        x-on:click="tab='observation'">

                                                    </div>
                                                    <span class="mt-2 text-black">{{ $pls->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>





                                    </div>
                                    <div class="flex flex-col items-center justify-center gap-10 mt-6 italic font-semibold"
                                        x-show="tab=='observation'">
                                        <div class="grid grid-cols-3 gap-8">
                                            @foreach ($allObservations as $obs)
                                                <div class="flex flex-col items-center justify-center w-[80px]">
                                                    <div class="rounded-full bg-yellow-300  p-[35px]">

                                                    </div>
                                                    <span class="mt-2 text-black">{{ $obs->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>



                                    <div class="flex items-center  justify-center pt-[20%] pb-4 bg-white">
                                        <a href="/add-new-place"
                                            class="flex items-center justify-center gap-2 px-4 py-3 text-lg font-extrabold text-white bg-blue-500 rounded-3xl">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="3" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Add new</a>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>


                </div>


            </div>
        </div>


        <input class="hidden" type="text" name="latitude" id="latitude" value="">
        <input class="hidden" type="text" name="longitude" id="longitude" value="">
    </div>

    <script>
        data = {!! json_encode($all_data) !!};
        userid = {!! json_encode($userid) !!};
        markers = {};
        let marker = null;
        let mymap0 = L.map('map').setView([48.6890, 7.14086], 6);
        // https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png

        osmLayer0 = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            minZoom: 0,
            maxZoom: 19,
            ext: 'png'
        }).addTo(mymap0);
        mymap0.zoomControl.remove();
        mymap0.addLayer(osmLayer0);
        mymap0.touchZoom.enable();
        mymap0.scrollWheelZoom.enable();
        icon = L.icon({
            iconUrl: '/img/openspace.png',
            iconSize: [25, 25],
            iconAnchor: [25, 25],
            popupAnchor: [0, -25]
        });





        if (navigator.geolocation) {
            //wait 3 seconds to get position
            console.log(getposition(success, fail));

        } else {
            alert("Geolocation is not supported by this browser.");
        }


        /*  function getposition() {
              navigator.geolocation.getCurrentPosition(
                  (position) => {
                      mymap0.setView([position.coords.latitude, position.coords.longitude], 10);
                      L.marker([position.coords.latitude, position.coords.longitude], {
                          icon: icon
                      }).addTo(mymap0);
                  },
                  (e) => {
                     $.getJSON('https://ipinfo.io/geo', function(response) {
          var loc = response.loc.split(',');
          var coords = {
              latitude: loc[0],
              longitude: loc[1]
          };
           mymap0.setView([coords.latitude, coords.longitude], 10);
                      L.marker([coords.latitude, coords.longitude], {
                          icon: icon
                      }).addTo(mymap0);
          });
                  }, {
                      enableHighAccuracy: true,
                  }
              );
          }
          */

        function getposition(success, fail) {

            var is_echo = false;
            if (navigator && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;
                        document.getElementById('latitude').value = pos.coords.latitude.toFixed(6);
                        document.getElementById('longitude').value = pos.coords.longitude.toFixed(6);
                        success(pos.coords.latitude, pos.coords.longitude);
                    },
                    function() {
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;
                        fail();
                    }
                );
            } else {
                fail();
            }

        }




        function success(lat, lng) {
            mymap0.setView([lat, lng], 10);
            L.marker([lat, lng], {
                icon: icon
            }).addTo(mymap0);
        }

        function fail() {
            alert("location failed");
        }



        console.log(data);

        let count = 0;
        for (let i = 0; i < data.length; i++) {

            count = count + 1;
            place = data[i];
            placeid = place.place_id;


            if (place.user_id == userid) {
                icon2 = L.icon({
                    iconUrl: '/img/marker.png',
                    iconSize: [25, 25],
                    iconAnchor: [25, 25],
                    popupAnchor: [0, -25]
                });
            } else {
                icon2 = L.divIcon({
                    className: 'custom-marker',
                    html: `<div class="circle-image" style="background-image: url('/img/marker.png')"></div>`,
                    iconSize: [40, 40], // Size of the icon [width, height]
                    iconAnchor: [20, 20] // Position of the icon relative to its container [x, y]
                });
            }


            placename = place.place.name;
            // pics = place.image0;
            placelatitude = place.latitude;
            placelongitude = place.longitude;
            if (place.user_id == userid) {
                // placetype = place.type.toLowerCase();
                var url = "place";
                var message = '';
                var readmore = '{{ __('messages.Edit this place') }}';
            } else {
                var url = "details";
                var message = '{{ __('messages.React to this place to earn 1 point!') }}';
                var readmore = '{{ __('messages.Read more') }}';
            }
            markerx = L.marker([placelatitude, placelongitude], {
                icon: icon2
            }).addTo(mymap0).bindPopup(
                '<div class="flex flex-col justify-center text-xl font-bold text-center text-black rounded-xl"><p id="title" class="px-4 text-sm">' +
                message +
                '</p></div><a href="" class="flex justify-center px-2 py-2 text-center bg-blue-600 rounded"><button class="text-white">' +
                readmore + '</button><a>'
            );

            markers[place.id] = markerx;
        }

        function mylocation() {
            getmyposition(success, fail);
        }


        function getmyposition(success, fail) {

            var is_echo = false;
            if (navigator && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        console.log(pos)
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;

                        success(pos.coords.latitude, pos.coords.longitude);
                    },
                    function() {
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;
                        fail();
                    }
                );
            } else {
                fail();
            }
        }

        function success(lat, lng) {
            mymap0.flyTo([lat, lng], 19);
            L.marker([lat, lng], {
                icon: icon
            }).addTo(mymap0);
        }

        function fail() {
            alert("location failed");
        }

        function zoom() {
            myzoom(success, fail);
        }

        function myzoom(success, fail) {
            var is_echo = false;
            if (navigator && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;
                        success(pos.coords.latitude, pos.coords.longitude);
                    },
                    function() {
                        if (is_echo) {
                            return;
                        }
                        is_echo = true;
                        fail();
                    }
                );
            } else {
                fail();
            }
        }

        function success(lat, lng) {
            mymap0.flyTo([lat, lng], 16);
            L.marker([lat, lng], {
                icon: icon
            }).addTo(mymap0);
        }

        function fail() {
            alert("location failed");
        }

        function see() {
            console.log('sdsd')
            var btnid = document.getElementById("othertag");
            btnid.click();
        }


        function select_place(place_id) {
            place = place_id;

            if (observation) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                latitude = document.getElementById('latitude').value;
                longitude = document.getElementById('longitude').value;

                $.ajax({
                    type: 'POST',
                    url: "{{ route('add.new.place') }}",
                    data: {
                        place: place,
                        observation: observation,
                        lat: latitude,
                        long: longitude,
                    },
                    success: function(data) {
                        alert(data.msg)
                        window.location.href = "/";

                        place = '';
                        observation = '';
                    }
                });
            }




        }

        function select_observation(observation_id) {

            observation = observation_id;
            latitude = document.getElementById('latitude').value;
            longitude = document.getElementById('longitude').value;


            if (place) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $.ajax({
                    type: 'POST',
                    url: "{{ route('add.new.place') }}",
                    data: {
                        place: place,
                        observation: observation,
                        lat: latitude,
                        long: longitude,
                    },
                    success: function(data) {
                        alert(data.msg)
                        window.location.href = "/";

                        place = '';
                        observation = '';
                    }
                });
            }






        }
    </script>
    <style>

    </style>
@endsection
