import React from 'react';
import {
  Text, GradientBlock, SmallTile,
} from 'components';
import {
  ScrollView, StyleSheet, View, TouchableOpacity,FlatList
} from 'react-native';
import { getNProducts } from 'mocks/products';
import { scale } from 'react-native-size-matters';
import Icon from 'react-native-vector-icons/Feather';
import Colors from 'themes/colors';
import PropTypes from 'prop-types';
import baseUrl from '../../../../assets/common/baseUrl';
import axios from 'axios';

const styles = StyleSheet.create({
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: scale(14),
    paddingHorizontal: scale(14),
    justifyContent: 'space-between',
  },
  icon: {
    marginRight: scale(10),
  },
  flash: {
    alignItems: 'center',
    flexDirection: 'row',
  },
  products: {
    paddingVertical: scale(14),
  },
});

const FlashSale = ({ navigation }) => {
  const[sale , setSale] = React.useState([]);
axios
.get(`${baseUrl}products/sale/8`)
  .then((res)=>{
    setSale(res.data.Data)
    // console.log(res.data.Data)
  })
  .catch((error)=>{
    console.log("something wrong")
  })

  const renderItem=({item,index})=>{
    return(
      <SmallTile
          key={item.id}
          style={StyleSheet.flatten([
            { marginRight: scale(7) },
            index === 0 && { marginLeft: scale(14) },
            index === 7 && { marginRight: scale(14) },
          ])}
          {...item}
          label={`From ${item.price}`}
          onPress={() => navigation.navigate('Category', { title: `From ${item.price}` })}
        />
    )
  }
return(
  <GradientBlock>
    <View style={styles.header}>
      <View style={styles.flash}>
        <Icon name="zap" size={scale(20)} color={Colors.white} style={styles.icon} />
        <Text weight="medium" font="h2" color="white">Flash Sales</Text>
      </View>
      <TouchableOpacity>
        <Text color="white">View all</Text>
      </TouchableOpacity>
    </View>
    <ScrollView
      horizontal
      showsHorizontalScrollIndicator={false}
      contentContainerStyle={styles.products}
    >
      {/* {sale.map((product, index) => (
        <SmallTile
          key={product.id}
          style={StyleSheet.flatten([
            { marginRight: scale(7) },
            index === 0 && { marginLeft: scale(14) },
            index === 7 && { marginRight: scale(14) },
          ])}
          {...product}
          label={`From ${product.price}`}
          onPress={() => navigation.navigate('Category', { title: `From ${product.price}` })}
        />
      ))} */}
      <FlatList
    numColumns={8}
    data={sale}
    renderItem={renderItem}
    />
    </ScrollView>
    
  </GradientBlock>
)};

FlashSale.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default FlashSale;
