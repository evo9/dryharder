<?php


namespace Dryharder\Models;


use Eloquent;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @method static OrderRequest wherePhone
 * @method static OrderRequest orderBy
 * @method static OrderRequest get
 * @method static OrderRequest whereState
 * @method static OrderRequest find
 * @method static OrderRequest[] all
 */
class OrderRequest extends Eloquent
{
    public $table = 'order_requests';

    public static function markAsCompleted($phone, $id)
    {

        self::wherePhone($phone)
            ->whereState(0)
            ->update([
                'state'    => 1,
                'order_id' => $id,
            ]);

    }

    public function getHumanId()
    {
        return 'R-' . str_repeat('0', 5 - strlen($this->id)) . $this->id;
    }

    public function reviewId()
    {
        $review = OrderReview::whereDocNumber($this->getHumanId())->first();
        if ($review) {
            return $review->id;
        }

        return null;
    }
}