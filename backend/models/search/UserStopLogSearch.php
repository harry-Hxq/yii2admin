<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserStopLog;


/**
 * UserStopLogSearch represents the model behind the search form of `backend\models\UserStopLog`.
 */
class UserStopLogSearch extends UserStopLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'create_time', 'update_time', 'status','is_tip'], 'integer'],
            [['latitude', 'longitude', 'precision'], 'number'],
            [['username','remark'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserStopLog::find();
        $query -> leftJoin('yii2_user','yii2_user.uid = yii2_user_stop_log.uid');

        $query->select("yii2_user_stop_log.*, yii2_user.username");

        // add conditions that should always apply here
        $query -> orderBy('yii2_user_stop_log.create_time desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'yii2_user.username', trim($this->username)]);
        $query->andFilterWhere(['like', 'yii2_user_stop_log.remark', trim($this->remark)]);
        $query->andFilterWhere(['yii2_user_stop_log.status' =>  $this->status]);
        $query->andFilterWhere(['yii2_user_stop_log.is_tip' =>  $this->is_tip]);

        return $dataProvider;
    }
}
