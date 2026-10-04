<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Layer-based perfume customizer.
 *
 * The live preview is a 3:4 canvas on which transparent component images are
 * stacked with CSS. Every layer is positioned in canvas percentages: `top` is
 * a % of the canvas height, `left` and `width` are % of its width (height
 * follows the image's own aspect ratio), and `z` is the stacking order.
 */
return new class extends Migration
{
    public function up(): void
    {
        $layerColumns = function (Blueprint $table, float $top, float $left, float $width, int $z, string $after): void {
            $table->decimal('layer_top', 6, 2)->default($top)->after($after);
            $table->decimal('layer_left', 6, 2)->default($left)->after('layer_top');
            $table->decimal('layer_width', 6, 2)->default($width)->after('layer_left');
            $table->smallInteger('layer_z')->default($z)->after('layer_width');
        };

        Schema::table('bottles', function (Blueprint $table) use ($layerColumns): void {
            $layerColumns($table, 30, 27, 46, 10, 'image');
            // Label box printed on the glass: centre point + width.
            $table->decimal('label_top', 6, 2)->default(66)->after('layer_z');
            $table->decimal('label_left', 6, 2)->default(50)->after('label_top');
            $table->decimal('label_width', 6, 2)->default(30)->after('label_left');
        });

        Schema::table('caps', function (Blueprint $table) use ($layerColumns): void {
            $layerColumns($table, 20, 41.7, 16.6, 30, 'image');
        });

        Schema::table('fragrances', function (Blueprint $table) use ($layerColumns): void {
            // Optional transparent "liquid" layer drawn inside the bottle.
            $table->string('liquid_image')->nullable()->after('image');
            $layerColumns($table, 30, 27, 46, 20, 'liquid_image');
        });

        // A cap/liquid that sits right on one bottle can be off on a taller or
        // wider one -- per-bottle overrides fix each combination without
        // editing images. Absent row = the layer's own default position.
        Schema::create('customizer_layer_overrides', function (Blueprint $table): void {
            $table->id();
            $table->string('bottle_id');
            $table->enum('layer_type', ['CAP', 'FRAGRANCE']);
            $table->string('layer_id');
            $table->decimal('layer_top', 6, 2);
            $table->decimal('layer_left', 6, 2);
            $table->decimal('layer_width', 6, 2);
            $table->smallInteger('layer_z');
            $table->timestamps();

            $table->foreign('bottle_id')->references('id')->on('bottles')->cascadeOnDelete();
            $table->unique(['bottle_id', 'layer_type', 'layer_id'], 'layer_overrides_unique');
        });

        // Which components a CUSTOM_PERFUME product offers. No rows of a type
        // = every active component of that type is offered.
        Schema::create('product_customizer_options', function (Blueprint $table): void {
            $table->id();
            $table->string('product_id');
            $table->enum('option_type', ['FRAGRANCE', 'BOTTLE', 'CAP']);
            $table->string('option_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->unique(['product_id', 'option_type', 'option_id'], 'customizer_options_unique');
        });

        // Order snapshot: exact IDs so the selection can be reconstructed, the
        // price split, and the assembled layer stack as it looked at purchase.
        // Still no foreign keys -- order history must survive master deletes.
        Schema::table('order_items', function (Blueprint $table): void {
            $table->string('product_id')->nullable()->after('product_type');
            $table->string('size_id')->nullable()->after('size_name');
            $table->string('fragrance_id')->nullable()->after('fragrance_name');
            $table->string('bottle_id')->nullable()->after('bottle_name');
            $table->string('cap_id')->nullable()->after('cap_name');
            $table->decimal('fragrance_price', 10, 2)->unsigned()->default(0)->after('base_price');
            $table->decimal('customization_price', 10, 2)->unsigned()->default(0)->after('cap_price');
            $table->json('preview')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['product_id', 'size_id', 'fragrance_id', 'bottle_id', 'cap_id', 'fragrance_price', 'customization_price', 'preview']);
        });
        Schema::dropIfExists('product_customizer_options');
        Schema::dropIfExists('customizer_layer_overrides');
        Schema::table('fragrances', function (Blueprint $table): void {
            $table->dropColumn(['liquid_image', 'layer_top', 'layer_left', 'layer_width', 'layer_z']);
        });
        Schema::table('caps', function (Blueprint $table): void {
            $table->dropColumn(['layer_top', 'layer_left', 'layer_width', 'layer_z']);
        });
        Schema::table('bottles', function (Blueprint $table): void {
            $table->dropColumn(['layer_top', 'layer_left', 'layer_width', 'layer_z', 'label_top', 'label_left', 'label_width']);
        });
    }
};
