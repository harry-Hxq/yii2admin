<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modelsgii\Route;

/**
 * RouteSearch represents the model behind the search form about `common\models\Route`.
 */
class RouteSearch extends Route
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'type', 'time_type', 'remark'], 'integer'],
            [['remark','route_date'], 'safe'],
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
        $query = Route::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query -> orderBy('route_date desc');
        $query->andFilterWhere([
            'type' => $this->type,
            'time_type' => $this->time_type,
        ]);
        if($this -> route_date){
            $query ->andFilterWhere(['route_date' => strtotime($this->route_date)]);
        }

        $query ->andFilterWhere(['like', 'remark', $this->remark]);



        return $dataProvider;
    }
}
