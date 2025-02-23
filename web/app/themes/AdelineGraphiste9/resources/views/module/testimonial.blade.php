@php
  use WP_Rplg_Google_Reviews\Includes\Core\Core as Review;
  $review = new Review();
    $reviews = ($review->get_reviews(get_post(63), false));

@endphp
<section class="testimonials">
  <h2 class="testimonials__title">Témoignages</h2>
  <p class="testimonials__subtitle">
    Parce que les avis positifs, c'est toujours important...
  </p>

  {!! do_shortcode('[grw id=63]') !!}

  <!-- Swiper principal -->
  <div class="swiper testimonials__swiper">
    <div class="swiper-wrapper">
      @foreach ($reviews['reviews'] as $review)
        <!-- Slide 1 -->
        <article class="testimonial swiper-slide">
          <header class="testimonial__header">
            <div class="testimonial__author-info">
              <h3 class="testimonial__author">{{$review->author_name}}</h3>
              {{--            <p class="testimonial__company">Entreprise</p>--}}
            </div>

            <div class="testimonial__rating">
              @foreach(array(1,2,3,4,5) as $val)
                @php  $score = $review->rating - $val;
            if ($score >= 0) {
                ?><span class="wp-star"><svg width="17" height="17" viewBox="0 0 1792 1792" role="none"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#fb8e28"></path></svg></span><?php
            } else if ($score > -1 && $score < 0) {
                ?><span class="wp-star"><svg width="17" height="17" viewBox="0 0 1792 1792" role="none"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="#fb8e28"></path></svg></span><?php
            } else {
                ?><span class="wp-star"><svg width="17" height="17" viewBox="0 0 1792 1792" role="none"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#ccc"></path></svg></span><?php
            } @endphp
              @endforeach
              <span class="testimonial__rating-value">{{$review->rating}} / 5</span>
            </div>
          </header>
          <blockquote class="testimonial__text">
            {{$review->text}}
          </blockquote>
        </article>
      @endforeach



      <!-- Ajoutez autant de slides que nécessaire -->
    </div>

    <!-- Flèches de navigation (optionnel) -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>

  <div class="testimonials__footer">
    <a href="https://search.google.com/local/writereview?placeid=ChIJ_5ZKXK2OAIgRnJdjElK4Pkk" class="btn btn--primary">Écrire
      un avis</a>
  </div>
</section>
