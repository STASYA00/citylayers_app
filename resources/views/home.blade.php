@php
    $locale = session()->get('locale');
    if ($locale == null) {
        $locale = 'en';
    }
@endphp
@extends('layouts.app')

@section('main')
    <style>
        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
        }
    </style>
    <div data-barba="container">
        @include('parts.navbar')
        <div class="flex flex-col mx-auto">
            <div class="pt-16">
                <div class="relative">
                    <div id="map" class="absolute mt-4 w-[100vw] z-10 h-[85vh]"></div>
                </div>
                <div x-data="{ modelOpen: false }">
                    <div
                        class="fixed bottom-0 w-full py-6 text-2xl font-bold text-black bg-[#B8E7EB] active:bg-blue-400 text-center z-50">
                        <a class="mx-12" @click="modelOpen =!modelOpen" onclick="zoom()">
                            {{ __('messages.Start Playing!') }}</a>

                    </div>


                    <div x-cloak x-show="modelOpen" class="fixed bottom-0 z-50 overflow-y-auto"
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
                                class="inline-block w-screen p-8 mt-60 overflow-hidden text-left transition-all transform bg-[#B8E7EB] shadow-xl z-50">

                                <div class="items-center space-x-4 bloc">

                                    <h1 class="text-3xl font-bold text-center text-black">
                                        {{ __('messages.This space is...') }}</h1>
                                    <div class="flex flex-col pt-6">
                                        <div class="flex justify-center">

                                            <a href="street"> <button
                                                    class="w-32 h-32 mx-4 py-6 text-gray-100 bg-[#55C5CF] hover:bg-blue-400  focus:outline-none hover:text-gray-200 rounded-full">
                                                    <i
                                                        class="fa-solid fa-road"></i><br>{{ __('messages.a street') }}</button>
                                            </a>
                                        </div>
                                        <div class="flex justify-center space-x-6">
                                            <a href="building">
                                                <button
                                                    class="w-32 h-32 py-6 text-gray-100 bg-[#55C5CF] hover:bg-blue-400 rounded-full focus:outline-none hover:text-gray-200">
                                                    <i
                                                        class="fa-solid fa-building"></i><br>{{ __('messages.a building') }}</button>
                                            </a>
                                            <a href="openspace">
                                                <button
                                                    class="w-32 h-32  py-6 text-gray-100 bg-[#55C5CF] hover:bg-blue-400 rounded-full focus:outline-none hover:text-gray-200">
                                                    <i
                                                        class="fa-solid fa-street-view"></i><br>{{ __('messages.an open space') }}</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div x-data="{ modelOpen: false }">
            <button id="point" @click="modelOpen =!modelOpen" class="hidden"></button>

            <div x-cloak x-show="modelOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center px-4 text-center">
                    <div x-cloak x-show="modelOpen" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="z-50 inline-block overflow-hidden transition-all transform bg-white rounded-lg shadow-xl mt-60">

                        <div class="items-center px-2 pt-3 space-x-4 bloc">
                            <div class="flex justify-center font-bold">
                                {{ __('messages.You have earned') }} <img src="/img/plus1.png"
                                    class="w-8 h-8 pb-2">{{ __('messages.point!') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-data="{ modelOpen: false }">
            <button id="already" @click="modelOpen =!modelOpen" class="hidden"></button>

            <div x-cloak x-show="modelOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center px-4 text-center">
                    <div x-cloak x-show="modelOpen" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="z-50 inline-block overflow-hidden transition-all transform bg-white rounded-lg shadow-xl mt-60">

                        <div class="items-center px-2 py-3 space-x-4 bloc">
                            <div class="flex justify-center font-bold">
                                {{ __('messages.You have already react to this place!') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-data="{ modelOpen: false }">
            <button id="commented" @click="modelOpen =!modelOpen" class="hidden"></button>

            <div x-cloak x-show="modelOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center px-4 text-center">
                    <div x-cloak x-show="modelOpen" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="z-50 inline-block overflow-hidden transition-all transform bg-white rounded-lg shadow-xl mt-60">

                        <div class="items-center px-2 py-3 space-x-4 bloc">
                            <div class="flex justify-center font-bold">
                                Your comment as been saved
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="bg-white modal">
        <div class="flex flex-row justify-between pt-2">
            <i id="closemodal" class="mt-2 ml-4 text-2xl text-gray-900 fas fa-close"></i>
        </div>
        <input id="placeid" type="text" class="hidden" value="">
        <input id="placetype" type="text" class="hidden" value="">
        <div class="pt-2 modal-content">
            <h1 id="title" class="text-xl font-bold text-center">
                {{ __('messages.React to this place to earn 1 point!') }}</h1>
            <div class="relative pt-4">
                <img id="img" src="" alt="image" class="object-cover w-full h-auto mx-auto">
                <img id="feeling" src="" alt="feeling" class="absolute bottom-0 right-0 w-auto h-12 m-4">
            </div>
            <div id="opinions" class="pt-4"></div>
            <p id="description" class="p-2 m-2 text-base font-bold"></p>
            <div class="pt-4">
                <img id="img2" src="" alt="image" class="object-cover w-full h-auto mx-auto">
            </div>
            <p id="description2" class="p-2 m-2 text-base font-bold"></p>
            <h1 id="title2" class="text-xl font-bold text-center pb-4">{{ __('messages.Your opinion') }}:</h1>
            <div class="flex flex-row justify-center pb-8">
                <img src="/img/1.png" class="w-8 h-8 mx-4 hover:scale-110 active:scale-125" onclick="mapAction('like')">
                <img src="/img/2.png" class="w-8 h-8 mx-4 hover:scale-110 active:scale-125"
                    onclick="mapAction('dislike')">
                <img src="/img/3.png" class="w-8 h-8 mx-4 hover:scale-110 active:scale-125" onclick="mapAction('stars')">
                <img src="/img/4.png" class="w-8 h-8 mx-4 hover:scale-110 active:scale-125" onclick="mapAction('bof')">
                <img src="/img/5.png" class="w-8 h-8 mx-4 hover:scale-110 active:scale-125" onclick="mapAction('weird')">
            </div>
            <div class="flex flex-col items-center justify-center w-full p-2 mb-16">
                <textarea name="comm" style="overflow:auto;resize:none" id="comm" cols="10" rows="2"
                    class="w-full mx-2 mb-4 font-light border rounded focus:outline-none focus:border-blue-300" placeholder=""></textarea>
                <button type="button"
                    class="w-1/2 px-2 py-2 mx-auto mt-1 text-xs text-white bg-gray-400 rounded-lg focus:outline-none focus:shadow-outline active:bg-gray-500"
                    onclick="comment()">Leave a comment</button>
            </div>
        </div>
    </div>
    <script>
        data = {!! json_encode($all_data) !!};
        markers = {};
        let marker = null;
        let mymap0 = L.map('map').setView([48.6890, 7.14086], 17);
        // https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png

        osmLayer0 = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            minZoom: 0,
            maxZoom: 19,
            ext: 'png'
        }).addTo(mymap0);
        mymap0.addLayer(osmLayer0);
        mymap0.touchZoom.enable();
        mymap0.scrollWheelZoom.enable();
        icon = L.icon({
            iconUrl: '/img/marker.png',
            iconSize: [40, 40],
            iconAnchor: [40, 40],
            popupAnchor: [0, -40]
        });

        var legend = L.control({
            position: "topright"
        });
        legend.onAdd = function(mymap) {
            var div = L.DomUtil.create("div", "legend bg-gray-200 p-2 border border-gray-400 rounded");
            div.innerHTML +=
                '<button class="" onclick="mylocation()"><i class="pr-2 fa fa-location-arrow"></i><span>My location</span><br></button>';
            return div;
        };
        legend.addTo(mymap0);


        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                    mymap0.setView([position.coords.latitude, position.coords.longitude], 19);
                    L.marker([position.coords.latitude, position.coords.longitude], {
                        icon: icon
                    }).addTo(mymap0);
                },
                function(e) {}, {
                    enableHighAccuracy: true
                });
        }

        let count = 0;
        for (let i = 0; i < data.length; i++) {

            count = count + 1;
            place = data[i];
            var placeid = place.id;
            var placetype = place.type;
            if (placetype == "Street") {
                icon2 = L.icon({
                    iconUrl: '/img/street.png',
                    iconSize: [40, 40],
                    iconAnchor: [40, 40],
                    popupAnchor: [0, -40]
                });
            }
            if (placetype == "Openspace") {
                icon2 = L.icon({
                    iconUrl: '/img/openspace.png',
                    iconSize: [40, 40],
                    iconAnchor: [40, 40],
                    popupAnchor: [0, -40]
                });
            }
            if (placetype == "Building") {
                icon2 = L.icon({
                    iconUrl: '/img/building.png',
                    iconSize: [40, 40],
                    iconAnchor: [40, 40],
                    popupAnchor: [0, -40]
                });
            }



            var placename = place.name;
            var pics = place.image0;
            var pics2 = place.image;
            var placelatitude = place.latitude;
            var placelongitude = place.longitude;
            var description = place.description;
            var description2 = place.description2;
            var opinions = place.opinions;
            var feeling = place.feeling;

            markerx = L.marker([placelatitude, placelongitude], {
                icon: icon2
            }).addTo(mymap0);
            markers[place.id] = markerx;

            var modal = document.getElementById("myModal");

            // When the user clicks on the marker, open the modal
            markerx.on("click", function() {
                modal.style.display = "block";
                document.getElementById("placeid").value = placeid;
                document.getElementById("placetype").value = placetype;

                if (!pics) {
                    document.getElementById("img").src = "/img/empty.png";
                } else {
                    // Remove any leading slashes from the pics2 URL using the replace function
                    document.getElementById("img").src = "/storage" + pics;
                }

                if (!opinions) {
                    document.getElementById("opinions").innerHTML = "";
                } else {
                      const opinionsArray = opinions.split(",");
                    let buttonsHtml = "";

                    for (let i = 0; i < opinionsArray.length; i++) {
                        buttonsHtml += '<i class="p-2 m-2 text-sm font-bold text-white bg-blue-500 rounded-xl">' +
                            opinionsArray[i] + '</i>';
                    }
                    document.getElementById("opinions").innerHTML = buttonsHtml;
                }
                if (!feeling) {
                    document.getElementById("feeling").src = "";
                } else {
                document.getElementById("feeling").src = "/img/" + feeling + ".png";
                }

                if (!description) {
                    document.getElementById("description").innerHTML = "";
                } else {
                    document.getElementById("description").innerHTML = description;
                }

                if (!pics2) {
                    document.getElementById("img2").src = "/img/empty.png";
                } else {
                    // Remove any leading slashes from the pics2 URL using the replace function
                    document.getElementById("img2").src = "/storage" + pics2;
                }

               if (!description2) {
                    document.getElementById("description2").innerHTML = "";
                } else {
                    document.getElementById("description2").innerHTML = description2;
                }
            });
        }

        function missing() {
            document.getElementById("img").src = "/img/empty.png";
        }

        function missing2() {
            document.getElementById("img2").src = "/img/empty.png";
        }

        function mylocation() {
            navigator.geolocation.getCurrentPosition(function(position) {
                mymap0.flyTo([position.coords.latitude, position.coords.longitude], 19);
            });
        }




        function mapAction(action) {
            var id = document.getElementById("placeid").value;
            var type = document.getElementById("placetype").value;

            var url;
            switch (action) {
                case 'like':
                    url = "/place/like";
                    break;
                case 'dislike':
                    url = "/place/dislike";
                    break;
                case 'stars':
                    url = "/place/stars";
                    break;
                case 'bof':
                    url = "/place/bof";
                    break;
                case 'weird':
                    url = "/place/weird";
                    break;
                case 'ohh':
                    url = "/place/ohh";
                    break;
                case 'wtf':
                    url = "/place/wtf";
                    break;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: id,
                    type: type
                },
                success: function(data) {
                    if (data == 'already') {
                        document.getElementById('myModal').style.display = "none";
                        document.getElementById('already').click();
                        //close popup after 3 seconds
                        setTimeout(function() {
                            document.getElementById('already').click();
                        }, 1000);
                    } else {
                        document.getElementById('myModal').style.display = "none";
                        document.getElementById('point').click();
                        //close popup after 3 seconds
                        setTimeout(function() {
                            document.getElementById('point').click();
                        }, 1000);
                    }
                }
            });
            mymap0.closePopup();
        }

        function comment() {
            var id = document.getElementById('placeid').value;
            var type = document.getElementById('placetype').value;
            var comment = document.getElementById('comm').value;
            var url = "/place/comment";
            console.log(comment)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    id: id,
                    type: type,
                    comment: comment
                },
                success: function(data) {
                    if (data == 'commented') {
                        document.getElementById('myModal').style.display = "none";
                        document.getElementById('commented').click();
                        //close popup after 3 seconds
                        setTimeout(function() {
                            document.getElementById('commented').click();
                        }, 1000);
                    }
                }
            });
            mymap0.closePopup();
        }

        function zoom() {
            navigator.geolocation.getCurrentPosition(function(position) {
                mymap0.flyTo([position.coords.latitude, position.coords.longitude], 16);
            });
        }
        var closemodal = document.getElementById('closemodal');

        closemodal.addEventListener("click", function() {
            document.getElementById('myModal').style.display = "none";

        });
    </script>
@endsection
