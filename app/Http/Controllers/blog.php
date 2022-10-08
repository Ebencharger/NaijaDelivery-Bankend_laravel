<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class blog extends Controller
{
    //
    public function blog(){
        $bigImage = url('image/Blog/unsplash_yW9jdBmE1BY.png');
        $imageOne = url('image/Blog/unsplash_dHPZ27-fbqE.png');
        $imageTwo = url('image/Blog/unsplash_Qj6BZn7OsLI (1).png');
        $imageThree = url('image/Blog/unsplash_Qj6BZn7OsLI.png');
        $blog = [
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $bigImage,
                'date' => 'Wed 16, 2022',
                'time' => '11:09am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageOne,
                'date' => 'Wed 16, 2022',
                'time' => '11:29am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageTwo,
                'date' => 'Wed 16, 2022',
                'time' => '11:39am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageThree,
                'date' => 'Wed 16, 2022',
                'time' => '11:29am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageOne,
                'date' => 'Wed 16, 2022',
                'time' => '11:19am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageTwo,
                'date' => 'Wed 16, 2022',
                'time' => '11:49am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
            [
                'title' => 'Healthy Meals for Healthy Growth',
                'post' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots',
                'image' => $imageThree,
                'date' => 'Wed 16, 2022',
                'time' => '11:59am',
                'read' => 'smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots.smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots. smes or a corporate organisation, we are here to serve you, get in touch with us to secure your regular delivery slots'
            ],
    
        ];
        return json_encode($blog);
    }
}
