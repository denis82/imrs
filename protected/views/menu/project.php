						<!-- User menu -->
						<div class="sidebar-user">
							<div class="category-content">
								<div class="media">
									<a href="<?= Yii::app()->urlManager->createUrl('project/index/view', array('id' => $model->id)); ?>" class="media-left"><img src="<?= ($favicon = $model->favicon()) ? $favicon->image : '/html/assets/images/placeholder.jpg' ?>" class="img-circle img-sm" alt=""></a>
									<div class="media-body">
										<a href="<?= Yii::app()->urlManager->createUrl('project/index/view', array('id' => $model->id)); ?>"><span class="media-heading text-semibold"><?= $model->name ?></span></a>
										<div class="text-size-mini text-muted text-ellipsis">
											<i class="icon-pin text-size-small"></i> 
											&nbsp;<? 
											$region_names = array();
											if ($model->regions and is_array($model->regions)) {
												foreach ($model->regions as $i) {
													$r = Region::getByPk($i);
													if ($r) $region_names[] = $r;
												}
											}

											print implode(', ', $region_names);
											?>
										</div>
									</div>

									<div class="media-right media-middle">
										<ul class="icons-list">
											<li>
												<a href="<?= Yii::app()->urlManager->createUrl('project/index/update', array('id' => $model->id)); ?>"><i class="icon-cog3"></i></a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- /user menu -->
