<?php

namespace Models;

class PromotionFeedback extends AbstractModel
{
	protected $id;
	protected $promotion;
	protected $subscription;
	protected $sent = 1;
	protected $viewed = 0;
	protected $rating;
	protected $review;
	protected $best_track_id;
	protected $support;
	protected $downloads;
	protected $updated_at;
	protected $created_at;

	public function availableDownloads() {

		return 3 - $this->downloads;
	}
}
